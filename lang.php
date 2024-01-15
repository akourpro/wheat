<!DOCTYPE html>
<html>

<head>
    <title>محول اللغة</title>
</head>

<body>
    <form method="post">
        الكلمة الرئيسية:
        <input type="text" name="keyword" required><br><br>
        الكلمة بالإنجليزية:
        <input type="text" name="english_word" required><br><br>
        الكلمة بالعربية:
        <input type="text" name="arabic_word" required><br><br>
        <input type="submit" name="submit" value="اعتماد">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $keyword = strtolower($_POST['keyword']);
        $english_word = $_POST['english_word'];
        $arabic_word = $_POST['arabic_word'];

        $en_file_content = file_get_contents('includes/lang/en.php');
        $ar_file_content = file_get_contents('includes/lang/ar.php');
        if (strpos($en_file_content, '$lang[\'' . $keyword . '\']') === false and strpos($ar_file_content, '$lang[\'' . $keyword . '\']') === false) {
            $en_file = fopen("includes/lang/en.php", "a");
            fwrite($en_file, '$lang[\'' . $keyword . '\'] = "' . $english_word . '";' . PHP_EOL);
            fclose($en_file);

            $ar_file = fopen("includes/lang/ar.php", "a");
            fwrite($ar_file, '$lang[\'' . $keyword . '\'] = "' . $arabic_word . '";' . PHP_EOL);
            fclose($ar_file);

            echo 'Added !<br>$lang["'.$keyword.'"]';
        } else {
            echo "الكلمة الرئيسية موجودة بالفعل في الملفات.";
        }
    }
    ?>

</body>

</html>