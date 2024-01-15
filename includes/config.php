<?php

/**
 * SHOW OR HIDE ERRORS
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
// error_reporting(E_ALL);

/**
 * DB CONFIG
 */
define("HOST", "localhost");     // The host you want to connect to.
define("USER", "root");    // The database username. 
define("PASSWORD", "");    // The database password. 
define("DATABASE", "");    // The database name. 
define("CHARSET", "utf8");    // The database name. 
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_FOUND_ROWS => true,
];
$con = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE . ";charset=" . CHARSET, USER, PASSWORD, $options);

/**
 * LANG FILES
 */
switch ($_COOKIE['lang']) {
    case "ar":
        $lango = "ar";
        include_once 'includes/lang/ar.php';
        break;
    case "en":
        $lango = "en";
        include_once 'includes/lang/en.php';
        break;
    default:
        $lango = "ar";
        include_once 'includes/lang/ar.php';
}
