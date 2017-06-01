<?php
$sa = $_SERVER["SERVER_ADDR"];
$sn = $_SERVER["SERVER_NAME"];
if ( $sa == "::1"):
	$sa = "localhost";
endif;
include_once($sa."_".$sn.".ini.php");
?>