<?php

include_once "includes/libs/mysqldump/autoload.php";

use Ifsnop\Mysqldump as IMysqldump;

try {
    $dump = new IMysqldump\Mysqldump('mysql:host=' . HOST . ';dbname=' . DATABASE, USER, PASSWORD);
    $dump->start('db.sql');
    echo "Backup complete!";
} catch (\Exception $e) {
    echo 'mysqldump error: ' . $e->getMessage();
}
