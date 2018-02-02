<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getFuncoes(){
	$arr = array();
	
	
	$result = CONN::get()->Execute("
		SELECT ID, DS_URL
		  FROM TAB_FUNCTION
	  ORDER BY DS_URL");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => ($fields['DS_URL'])
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}
?>