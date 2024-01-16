<?php

/**
 * AUTO LOAD WITH HEADER
 */

include_once 'includes/config.php';

include_once 'includes/functions.php';

include_once 'includes/csrf.php';
$csrf = new CSRF_Protect('_csrf', "Public_Users"); // Change "Public_Users" to any name


