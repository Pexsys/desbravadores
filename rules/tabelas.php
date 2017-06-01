<?php
@require_once("../include/functions.php");
responseMethod();

function getCamisetas(){
	$arr = array();
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT CD
		  FROM TAB_CAMISETA 
	  ORDER BY CD");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"cd" => $fields['CD']
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}

function getCargos(){
	$arr = array();
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT CD, DSM, DSF
		  FROM TAB_CARGO
	  ORDER BY CD");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"cd" => $fields['CD'],
			"dm" => utf8_encode($fields['DSM']),
			"df" => utf8_encode($fields['DSF'])
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}

function getUnidades(){
	$arr = array();
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM TAB_UNIDADE
	  ORDER BY DS");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => utf8_encode($fields['DS']),
			"tp" => $fields['TP'],
			"ie" => $fields['IDADE'],
			"cc" => $fields['CD_COR'],
			"ccg" => $fields['CD_COR_GENERO'],
			"fg" => $fields['FG_ATIVA']
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}

function getUFs(){
	$arr = array();
	fConnDB();
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID
		  FROM TAB_UF
	  ORDER BY ID");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID']
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}
?>