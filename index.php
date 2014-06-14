<?php 
define('APP_PATH', dirname(__FILE__)."/app/");
define('CLS_PATH', APP_PATH."cls/");
include CLS_PATH."core.cls.php";
core::run();
