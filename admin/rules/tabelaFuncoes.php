<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getFuncoes(){
	$arr = array();

	$result = $GLOBALS['conn']->Execute("
		SELECT ID, DS_NEW AS DS_URL
		  FROM TAB_FUNCTION
	  ORDER BY 1");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => $fields['DS_URL']
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}
?>
