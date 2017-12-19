<?php
//$sa = $_SERVER["SERVER_ADMIN"];
$su = $_SERVER["HTTP_X_USERNAME"];
$sn = $_SERVER["SERVER_NAME"];
include_once($su."_".$sn.".ini.php");
?>