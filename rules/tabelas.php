<?php
@require_once("../include/functions.php");
responseMethod();

function getAgasalhos(){
	return array( "result" => true, "source" => getTamanhos("A") );
}

function getCamisetas(){
	return array( "result" => true, "source" => getTamanhos("C") );
}

function getTamanhos( $tp ){
	$arr = array();

	$result = $GLOBALS['conn']->Execute("
		SELECT CD
		  FROM TAB_TAMANHOS
		 WHERE TP = ?
	  ORDER BY ORD", array($tp) );
	foreach ($result as $k => $fields):
		$arr[] = array(
			"cd" => $fields['CD']
		);
	endforeach;
	return $arr;
}

function getCargos(){
	$arr = array();

	$result = $GLOBALS['conn']->Execute("
		SELECT CD, DSM, DSF
		  FROM TAB_CARGO
	  ORDER BY CD");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"cd" => $fields['CD'],
			"dm" => ($fields['DSM']),
			"df" => ($fields['DSF'])
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}

function getUnidades(){
	$arr = array();

	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM TAB_UNIDADE
	  ORDER BY DS");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => ($fields['DS']),
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
