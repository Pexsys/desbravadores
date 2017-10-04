<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getPerfis(){
	$arr = array();
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS
		  FROM TAB_PERFIL
	  ORDER BY DS");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => ($fields['DS'])
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}
?>