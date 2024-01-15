<?php

include_once "includes/libs/sqlimport/autoload.php";

use Dcblogdev\SqlImport\Import;

new Import("db.sql", USER, PASSWORD, DATABASE, HOST, true, true);



echo "Imported successfully";
