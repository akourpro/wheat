<?php

include_once 'includes/libs/HTMLPurifier/HTMLPurifier.auto.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

include_once 'includes/libs/phpmailer/autoload.php';

/**
 * SELECT
 */
function dbSelect($table, $selects, $where = null, $vars = null)
{
	// db connect
	global $con;
	global $rows;
	global $countrows;
	// db query
	$stmt = $con->prepare("SELECT $selects FROM $table $where");
	$stmt->execute($vars);
	// return results
	$rows = $stmt->fetchAll();
	$countrows = $stmt->rowCount();
}

/**
 * INSERT
 */
function dbInsert($table, $columns, $vars)
{
	// db connect
	global $con;
	// db query
	$binds = substr(str_repeat('?,', count($vars)), 0, -1);
	$stmt = $con->prepare("INSERT INTO $table ($columns) VALUES ($binds)");
	$stmt->execute($vars);
	// return last Insert Id
	return $con->lastInsertId();
}

/**
 * UPDATE
 */
function dbUpdate($table, $columns, $vars, $where = null)
{
	// db connect
	global $con;
	// db query
	$stmt = $con->prepare("UPDATE $table SET $columns $where");
	$stmt->execute($vars);
	// return num rows affected
	return $stmt->rowCount();
}

/**
 * DELETE
 */
function dbDelete($table, $where = null, $vars = null)
{
	// db connect
	global $con;
	// db query
	$stmt = $con->prepare("DELETE FROM $table $where");
	$stmt->execute($vars);
	// return results
	return $stmt->rowCount();
}

/**
 * تنظيف المتغيرات من اكواد html
 * لمنع xss
 */
function safer($var)
{
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
	$var_clean = $purifier->purify(trim($var));
	return htmlspecialchars($var_clean);
}

// Check if Var is only numbers
function numer($var)
{
	if (preg_match("/^[0-9]*\.?[0-9]*$/", $var)) {
		return $var;
	} else {
		return 0;
	}
}

/**
 * جلب اعدادت الموقع
 */
function gsite()
{
	global $rows;
	global $site;
	dbSelect("settings", "name,value");
	foreach ($rows as $site_setting) {
		$site[$site_setting['name']] = safer($site_setting['value']);
	}
}
gsite(); // هذا السطر لتشغيل الأمر، احذفه للتعطيل

/**
 * get absoule path
 */
function getpath()
{
	global $site;
	if ($site["site_folder"]) {
		return $_SERVER["DOCUMENT_ROOT"] . "/" . $site["site_folder"] . "/";
	} else {
		return $_SERVER["DOCUMENT_ROOT"] . "/";
	}
}

/**
 * send emails with smtp
 */
function mailer($email, $subject, $body, $option = null)
{
	global $site;
	//Server settings
	$mail = new PHPMailer();
	$mail->isSMTP();
	// $mail->SMTPDebug = 2;
	$mail->SMTPAuth   = true;
	$mail->Host       = $site["smtp_host"];
	$mail->Username   = $site["smtp_user"];
	$mail->Password   = $site["smtp_pass"];
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Port       = 587;
	$mail->Priority   = 1;
	$mail->CharSet = 'UTF-8';
	//Recipients
	$mail->setFrom($site["smtp_user"], $site["site_name"]);
	$mail->addAddress($email);
	//Content
	$mail->isHTML(true);
	$mail->Subject = $subject;
	// html
	$mail->Body = $body;
	if (!$option) {
		$mail->send();
	} else {
		return $mail;
	}
}

/**
 * دوال عملية الدخول والتحقق
 */
function sec_session_start($workspace)
{
	global $site;
	$session_name = $workspace;   // Set a custom session name 
	$secure = true;
	// This stops JavaScript being able to access the session id.
	$httponly = true;
	// Forces sessions to only use cookies.
	if (ini_set('session.use_only_cookies', 1) === FALSE) {
		echo "<meta http-equiv='Refresh' content='0; url=" . safer($site["site_url"]) . "abma/auth/login'>";
		exit();
	}
	// Gets current cookies params.
	ini_set("session.gc_maxlifetime", 86400);
	ini_set("session.cookie_lifetime", 86400);
	$cookieParams = session_get_cookie_params();
	session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
	// Sets the session name to the one set above.
	session_name($session_name);
	session_start();            // Start the PHP session 
	session_regenerate_id();    // regenerated the session, delete the old one. 
}

/**
 * دالة التحقق من جلسة الإدارة
 */
function login_check_admin()
{
	global $rows;
	global $countrows;
	// Check if all session variables are set 
	if (!empty($_SESSION['user_id'])) {
		$user_id = numer($_SESSION['user_id']);
		$login_string = $_SESSION['login_string'];
		// Get the user-agent string of the user.
		$user_browser = $_SERVER['HTTP_USER_AGENT'];
		$user_browser = 1;
		dbSelect("admins", "password", "WHERE id=? LIMIT 1", [$user_id]);
		$user_pass = $rows[0]["password"];
		if ($countrows == 1) {
			$login_check = hash('sha512', $user_pass . $user_browser);
			if (hash_equals($login_check, $login_string)) {
				// Logged In!!!! 
				return true;
			} else {
				// Not logged in 
				return false;
			}
		} else {
			// Not logged in 
			return false;
		}
	} else {
		// Not logged in 
		return false;
	}
}
/**
 * عملية الدخول للإدارة
 */
function login_admin($user_email, $user_pass)
{
	global $rows;
	global $countrows;
	// Sanitize and validate the data passed in
	if (check($user_email, "email")) {
		return false;
	}
	if (strlen(hash('sha512', $user_pass)) != 128) {
		return false;
	}
	dbSelect("admins", "id, username, email, password", "WHERE email=? LIMIT 1", [$user_email]);
	if ($countrows == 1) {
		$user_id = $rows[0]["id"];
		$db_password = $rows[0]["password"];

		// Check if the password in the database matches
		// the password the user submitted. We are using
		// the password_verify function to avoid timing attacks.
		// The hashed password.
		$user_pass = hash('sha512', $user_pass);
		if (password_verify($user_pass, $db_password)) {
			// Password is correct!			
			$user_browser = $_SERVER['HTTP_USER_AGENT'];
			$user_browser = 1;
			// XSS protection as we might print this value
			$user_id = numer(preg_replace("/[^0-9]+/", "", $user_id));
			$_SESSION['user_id'] = $user_id;
			$_SESSION['login_string'] = hash('sha512', $db_password . $user_browser);
			// Login successful.
			return "success";
		} else {
			// Password is not correct
			return false;
		}
	} else {
		// No user exists.
		return false;
	}
}

/**
 * جلب معلومات المستخدم
 */
function guser()
{
	global $rows;
	global $user;
	dbSelect("users", "*", "WHERE id = ? LIMIT 1", [$_SESSION['user_id']]);
	$user = $rows[0];
}

/**
 * تقوم هذه الوظيفة بالتحقق من نوع المدخلات وتعود بالناتج false في حال كان خطأ
 */
function check($var, $type)
{
	if ($type == "num") {
		if (!preg_match("/^[0-9]+$/", $var)) {
			return false;
		}
	} elseif ($type == "email") {
		strtolower($type);
		if (!filter_var($var, FILTER_VALIDATE_EMAIL) or !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $var)) {
			return false;
		}
	} elseif ($type == "txt") {
		if (!preg_match("/^[a-zA-Z-\p{Arabic} ']*$/u", $var)) {
			return false;
		}
	} elseif ($type == "ar") {
		if (!preg_match("/^[\p{Arabic} ]+$/u", $var)) {
			return false;
		}
	} elseif ($type == "en") {
		if (!preg_match("/^[a-zA-Z-' ]*$/", $var)) {
			return false;
		}
	} elseif ($type == "url") {
		if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $var)) {
			return false;
		}
	}
}

/**
 * انشاء كود توكن لعملية ما مثلا تحقق بريد او تحقق كلمة سر
 * يقوم الكود انشاء كود جديد ويتأكد هل موجود في قاعدة سابقا ام لا
 * $size: حجم توكن ممكن  8 او 16 او اكثر حسب صعوبة كلما كان حجم اكثر راح يكون طويل
 * $table: الجدول الذي تحقق منه
 * $col: العمود الذي يتحقق منه
 * $type: token,id
 */
/**
 * انشاء كود رقمي وحيد نفس آلية عمل الدالة السابقة
 * لكن هذا يكون كود به ارقام فقط
 * مثلا لارقام طلبات وكذا
 * مكان $column سيتم وضع اسم الخلية
 */
function genCode($table, $column, $type, $size)
{
	global $countrows;
	if ($type == "token") {
		$code_id =  [bin2hex(random_bytes($size))];
	} else {
		$longid = abs(crc32(uniqid())) . abs(crc32(uniqid())) . abs(crc32(uniqid())) . abs(crc32(uniqid()));
		$code_id =  [substr($longid, 0, $size)];
	}
	dbSelect($table, "id", "Where $column=? LIMIT 1", $code_id);
	if ($countrows === 1) {
		do {
			if ($type = "token") {
				$code_id =  [bin2hex(random_bytes($size))];
			} else {
				$longid = abs(crc32(uniqid())) . abs(crc32(uniqid())) . abs(crc32(uniqid())) . abs(crc32(uniqid()));
				$code_id =  [substr($longid, 0, $size)];
			}
			dbSelect($table, "id", "Where $column=? LIMIT 1", $code_id);
		} while ($countrows === 1);
	}
	return $code_id[0];
}

/**
 * جلب نظام المستخدم من خلال HTTP_USER_AGENT
 * لكن يجب عدم الاعتماد عليه لانه يمكن تغييره من طرف المستخدم
 * يجب تنظيم المتغير عند عرضه او ادخاله في القاعدة
 */
function getOS()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$os_platform =   "Unknown";
	$os_array =   array(
		'/windows nt 10/i'      =>  'Windows 10',
		'/windows nt 6.3/i'     =>  'Windows 8.1',
		'/windows nt 6.2/i'     =>  'Windows 8',
		'/windows nt 6.1/i'     =>  'Windows 7',
		'/windows nt 6.0/i'     =>  'Windows Vista',
		'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		'/windows nt 5.1/i'     =>  'Windows XP',
		'/windows xp/i'         =>  'Windows XP',
		'/windows nt 5.0/i'     =>  'Windows 2000',
		'/windows me/i'         =>  'Windows ME',
		'/win98/i'              =>  'Windows 98',
		'/win95/i'              =>  'Windows 95',
		'/win16/i'              =>  'Windows 3.11',
		'/macintosh|mac os x/i' =>  'Mac OS X',
		'/mac_powerpc/i'        =>  'Mac OS 9',
		'/linux/i'              =>  'Linux',
		'/ubuntu/i'             =>  'Ubuntu',
		'/iphone/i'             =>  'iPhone',
		'/ipod/i'               =>  'iPod',
		'/ipad/i'               =>  'iPad',
		'/android/i'            =>  'Android',
		'/blackberry/i'         =>  'BlackBerry',
		'/webos/i'              =>  'Mobile'
	);
	foreach ($os_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$os_platform = $value;
		}
	}
	return $os_platform;
}

/**
 * جلب متصفح المستخدم من خلال HTTP_USER_AGENT
 * لكن يجب عدم الاعتماد عليه لانه يمكن تغييره من طرف المستخدم
 * يجب تنظيم المتغير عند عرضه او ادخاله في القاعدة
 */
function getBrowser()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$browser        = "Unknown";
	$browser_array  = array(
		'/msie/i'       =>  'Internet Explorer',
		'/firefox/i'    =>  'Firefox',
		'/safari/i'     =>  'Safari',
		'/chrome/i'     =>  'Chrome',
		'/edge/i'       =>  'Edge',
		'/opera/i'      =>  'Opera',
		'/netscape/i'   =>  'Netscape',
		'/maxthon/i'    =>  'Maxthon',
		'/konqueror/i'  =>  'Konqueror',
		'/mobile/i'     =>  'Handheld Browser'
	);
	foreach ($browser_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$browser = $value;
		}
	}
	return $browser;
}

/**
 * جلب عنوان IP المستخدم
 * لكن يجب عدم الاعتماد عليه لانه يمكن تغييره من طرف المستخدم
 * يجب تنظيم المتغير عند عرضه او ادخاله في القاعدة
 */
function getIP()
{
	$ip_address = '';
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip_address = $_SERVER['HTTP_CLIENT_IP']; // Get the shared IP Address
	} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		//Check if the proxy is used for IP/IPs
		// Split if multiple IP addresses exist and get the last IP address
		if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
			$multiple_ips = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip_address = trim(current($multiple_ips));
		} else {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	} else if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
		$ip_address = $_SERVER['HTTP_X_FORWARDED'];
	} else if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
		$ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
	} else if (!empty($_SERVER['HTTP_FORWARDED'])) {
		$ip_address = $_SERVER['HTTP_FORWARDED'];
	} else {
		$ip_address = $_SERVER['REMOTE_ADDR'];
	}
	return $ip_address;
}

/**
 * دالة توليد كلمة سر بشكل تلقائي
 * مع تحديد شروط كأحرف كبيرة وصغيرو وارقام ورموز
 * l: حروف صغيرى
 * u: حروف كبيرة
 * d: ارقام
 * s: حروف خاصة
 * الدالة بشكل افتراضي تنشئ كلمة سر بطول 8 حروف بكل الشروط generateStrongPassword()
 * اذا ادرت تخصص استعمل المتغيرات
 */
function tweak_array_rand($array)
{
	if (function_exists('random_int')) {
		return random_int(0, count($array) - 1);
	} elseif (function_exists('mt_rand')) {
		return mt_rand(0, count($array) - 1);
	} else {
		return array_rand($array);
	}
}
function genPass($length = 8, $available_sets = 'luds')
{
	$sets = array();
	if (strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if (strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if (strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
	if (strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';
	$all = '';
	$password = '';
	foreach ($sets as $set) {
		$password .= $set[tweak_array_rand(str_split($set))];
		$all .= $set;
	}
	$all = str_split($all);
	for ($i = 0; $i < $length - count($sets); $i++) {
		$password .= $all[tweak_array_rand($all)];
	}
	$password = str_shuffle($password);
	return $password;
}

/******
 * تقوم هذه الدالة بالتحقق من ان الجهاز المستخدم هو هاتف
 * لا يمكن الاعتماد عليها تماما لان المستخدم قادر على تغيير user agent 
 */

function isMobile()
{
	return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/**
 * تقوم هذه الوظيفة بإرجاع اشعار بتقنية bootstrap 5.3
 * حيث انها تستقبل الرسالة و نوع الاشعار و خيار ازالة الاشعار وهو اختياري
 * مثال: 
 * alerts("Thank you","success")
 * اذا اردت اظهار زر ازالة الاشعار
 * alerts("Thank you","success", 1)
 */
function alerts($text, $type, $close = false)
{
	$alert = '';
	if ($close) {
		$alert = '<div class="alert alert-' . $type . '">' . $text . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$alert = '<div class="alert alert-' . $type . '">' . $text . '</div>';
	}
	return $alert;
}

/**
 * دالة اضافة اشعار عند حفظ او اجراء تعديل لنجاح المهمة او خطأ ما
 * $type: success, error, warning, info, question
 * $title: اما نص او html
 * $text: اما نص او html ، لكن اذا كان html يجب وضع علامة 1 في خانة html
 * $link: رابط عند ضغط على زر، يمكن استعمال متغير here لتحديث الصفحة
 * $html: اذا كان المحتوى html اكتب 1 او true
 * $close: اذا ترد غلق الاشعار فقط بدون تحديث او توجيه اكتب 1 او true
 */
function sweet($type, $title, $text, $link = null)
{
	global $lang;
	switch ($type) {
		case 'error':
			$colorbtn = "#dc3545";
			break;
		case 'success':
			$colorbtn = "#218838";
			break;
		case 'warning':
			$colorbtn = "#ffc107";
			break;
		case 'info':
			$colorbtn = "#17a2b8";
			break;
		case 'question':
			$colorbtn = "#17a2b8";
			break;
		default:
			$colorbtn = "#218838";
	}
	if (!$link) {
		$result =  "window.close()";
	} else {
		if ($link == "here") {
			$result = "window . location = window.location.href";
		} else {
			$result = "window . location = '" . $link . "'";
		}
	}
	echo "<script>Swal.fire({icon: '$type',title: `$title`,html: `$text`, confirmButtonText: 'موافق!',confirmButtonColor: '$colorbtn',showCloseButton: true}).then((result)=>{if(result.isConfirmed){" . $result . "}else{" . $result . "}})</script>";
}

/**
 * FILTER AND SANITISE URL
 */
function escUrl($url)
{
	if ('' == $url) {
		return $url;
	}
	// Remove any invalid characters from the URL.
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
	// Strip any carriage returns or line feeds from the URL.
	$strip = array('%0d', '%0a', '%0D', '%0A');
	$url = (string) $url;
	$count = 1;
	while ($count) {
		$url = str_replace($strip, '', $url, $count);
	}
	// Encode any ampersands (`&`) and single quotes (`'`) as HTML entities.
	$url = str_replace(';//', '://', $url);
	$url = htmlentities($url);
	$url = str_replace('&amp;', '&#038;', $url);
	$url = str_replace("'", '&#039;', $url);
	$url = filter_var($url, FILTER_SANITIZE_URL);
	// Check to make sure that the URL starts with a slash (`/`).
	if ($url[0] !== '/') {
		return '';
	} else {
		return $url;
	}
}

/**
 * GET PAGE TITLE FROM PATH
 */
function setTitle($path)
{
	if ($path) {
		$path = rtrim($path, '.php');
		$paths = explode("/", $path);
		$paths = array_slice($paths, -2);
		return implode("_", $paths);
	} else {
		return "page_title";
	}
}

/***
 * دالة مخصصة لرفع الصور فقط
 * 
 * $input = اسم حقل input file
 * $max_size = الحجم الأقصى بالميجا
 * $dir = مسار الرفع
 */
function up($name, $input, $dir, $max_size)
{
	if (is_uploaded_file($_FILES[$input]['tmp_name']) and !empty($_FILES[$input])) {
		global $filename;
		// get file info	
		$file_path = $_FILES[$input]['tmp_name'];
		$file_size = filesize($file_path);
		$file_info = finfo_open(FILEINFO_MIME_TYPE);
		$file_type = finfo_file($file_info, $file_path);
		$file_ext  = strtolower(pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION));
		$max_size = $max_size * 1024 * 1024; // تحويل الميجابايت إلى بايت
		// check size
		if ($file_size === 0) {
			return "خطأ، الملف فارغ"; // اذا كان الملف فارغ
			die();
		}
		if ($file_size > $max_size) {
			return "خطأ، حجم الملف أكبر من $max_size ميجابايت"; // اذا كان الحجم أكبر من الحد المسموح به
			die();
		}
		// check file type
		$allowedTypes = ['image/png' => 'png', 'image/jpg' => 'jpg', 'image/jpeg' => 'jpeg', 'image/svg+xml' => 'svg'];
		if (!in_array($file_type, array_keys($allowedTypes)) or !in_array($file_ext, $allowedTypes)) {
			return "خطأ، امتداد الملف غير مسموح به";
			die();
		}
		if ($file_ext != "svg") {
			$imagesizeinfo = getimagesize($file_path);
			if (!in_array($imagesizeinfo['mime'], array_keys($allowedTypes))) {
				return "خطأ، امتداد الملف غير مسموح به";
				die();
			}
			if ($imagesizeinfo[0] == 0 or $imagesizeinfo[1] == 0 or empty($imagesizeinfo)) {
				return "خطأ، امتداد الملف غير مسموح به";
				die();
			}
			$imagesizeinfo = exif_imagetype($file_path);
			if ($imagesizeinfo != 2 and $imagesizeinfo != 3) {
				return "خطأ، امتداد الملف غير مسموح به";
				die();
			}
		}
		// file name and path
		// check filename 
		$check_filename = pathinfo($name, PATHINFO_EXTENSION);
		if (!empty($check_filename)) {
			$filename =  $name;
		} else {
			$extension = $allowedTypes[$file_type];
			$filename =  $name . "." . $extension;
		}
		if (!str_ends_with($dir, "/")) {
			$dir = $dir . "/";
		}
		$newFilepath = $dir . $filename;
		// move file
		if (move_uploaded_file($file_path, $newFilepath)) {
			return "uploaded_done"; // success
		} else {
			return "خطأ، فشل في رفع الملف، اعد المحاولة"; // فشل في رفع الملف
			die();
		}
	}
}

/**
 * دالة مخصصة لرفع الصور + الانواع الاخرى من الملفات
 */
function fileup($name, $input, $dir, $max_size)
{
	if (is_uploaded_file($_FILES[$input]['tmp_name']) and !empty($_FILES[$input])) {
		global $filename;
		// get file info	
		$file_path = $_FILES[$input]['tmp_name'];
		$file_size = filesize($file_path);
		$file_info = finfo_open(FILEINFO_MIME_TYPE);
		$file_type = finfo_file($file_info, $file_path);
		$file_ext  = strtolower(pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION));
		$max_size = $max_size * 1024 * 1024; // تحويل الميجابايت إلى بايت
		// check size
		if ($file_size === 0) {
			return "خطأ، الملف فارغ"; // اذا كان الملف فارغ
			die();
		}
		if ($file_size > $max_size) {
			return "خطأ، حجم الملف أكبر من $max_size ميجابايت"; // اذا كان الحجم أكبر من الحد المسموح به
			die();
		}
		// check file type
		$allowedTypes =
			[
				'image/png' => 'png',
				'image/jpg' => 'jpg',
				'image/jpeg' => 'jpeg',
				'image/svg+xml' => 'svg',
				'application/msword' => 'doc',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
				'application/vnd.ms-excel' => 'xls',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
				'application/vnd.ms-powerpoint' => 'ppt',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
				'text/csv' => 'csv',
				'application/x-rar-compressed' => 'rar',
				'application/x-7z-compressed' => '7z',
				'application/octet-stream' => 'iso',
				'text/plain' => 'txt',
				'application/pdf' => 'pdf',
				'application/zip' => 'zip'
			];
		if (!in_array($file_type, array_keys($allowedTypes)) or !in_array($file_ext, $allowedTypes)) {
			return "خطأ، امتداد الملف غير مسموح به";
			die();
		}
		// file name and path
		// check filename 
		$check_filename = pathinfo($name, PATHINFO_EXTENSION);
		if (!empty($check_filename)) {
			$filename =  $name;
		} else {
			$extension = $allowedTypes[$file_type];
			$filename =  $name . "." . $extension;
		}
		if (!str_ends_with($dir, "/")) {
			$dir = $dir . "/";
		}
		$newFilepath = $dir . $filename;
		// move file
		if (move_uploaded_file($file_path, $newFilepath)) {
			return "uploaded_done"; // success
		} else {
			return "خطأ، فشل في رفع الملف، اعد المحاولة"; // فشل في رفع الملف
			die();
		}
	}
}

/**
 * $date = ضع تاريخ الميلاد مثلا
 * تستخدم هذه الدالة لمعرفة الفترة منذ تاريخ محدد
 */
function age($date)
{
	$ToDate = date("Y-m-d H:i:s");
	return date_diff(date_create($date), date_create($ToDate))->y; // لاظهار عدد السنوات
	// return date_diff(date_create($date), date_create($ToDate))->m; // لاظهار عدد الشهور
	// return date_diff(date_create($date), date_create($ToDate))->d; // لاظهار عدد الايام
}


/**
 * تستخدم لاظهار تاريخ على شكل (منذ كذا دقيقة) تستخدم غالبًأ في الاشعارات
 * اذا وضعت ,true
 * سيتم طباعة التاريخ مفصل: 49 دقيقة، 19 ثانية مضت
 * 
 * اذا تركتها فارغة سيطبع: 49 دقيقة مضت
 */
function ago($datetime, $full = false)
{
	global $lang;

	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);
	$diff->{'w'} = floor($diff->d / 7);
	$diff->{'d'} -= $diff->{'w'} * 7;
	$string = array(
		'y' => $lang['year'],
		'm' => $lang['month'],
		'w' => $lang['week'],
		'd' => $lang['day'],
		'h' => $lang['hour'],
		'i' => $lang['minute'],
		's' => $lang['second'],
	);

	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	$result = $string ? implode($lang['comma'] . ' ', $string)  : $lang['now'];
	return $lang['since'] . " " . $result;
}

/**
 * check if array empty or not
 */
function notnull($array)
{
	if (empty($array)) {
		return false;
	}
	foreach ($array as $value) {
		if (is_null($value)) {
			return false;
		}
	}
	return true;
}


/**
 * date diff in days
 */

function dateDiffInDays($date1, $date2)
{
	// Calculating the difference in timestamps
	$diff = strtotime($date2) - strtotime($date1);
	// 1 day = 24 hours
	// 24 * 60 * 60 = 86400 seconds
	$diffround = round($diff / 86400);
	if ($diffround <= 0) {
		return 0;
	} else {
		return abs($diffround);
	}
}
