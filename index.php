<?php

/*
 *   ___         _                           ___ __  __ ___ 
 *  / __|___ _ _| |_ ___ _ _  __ _ _ _ _  _ / __|  \/  / __|
 * | (__/ -_) ' \  _/ -_) ' \/ _` | '_| || | (__| |\/| \__ \
 *  \___\___|_||_\__\___|_||_\__,_|_|  \_, |\___|_|  |_|___/
 *                                     |__/                 
 * *************************                       
 * Content: CentenaryCMS
 * Autor  : Revue
 * Contact: David@brogli.me
 * Version: 3.0  
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


error_reporting(1);
date_default_timezone_set('Europe/Berlin');
header('Content-Type: text/html; charset=UTF-8');

include('./classes/Config.php');
include('./classes/Mysqli.php');
include('./classes/MusManager.php');
include('./classes/class.staff.php');
include('./classes/FunctionsManager.php');
include('./classes/UserManager.php');
include('./classes/PageManager.php');
?>
