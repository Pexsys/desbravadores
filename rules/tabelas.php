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
	
	
	$result = CONN::get()->execute("
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
	
	$result = CONN::get()->execute("
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

function getRestricaoAlimentar(){
	$arr = array();
	
	$result = CONN::get()->execute("
		SELECT ID, DS
		  FROM TAB_TP_REST_ALIM
	  ORDER BY DS");
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => ($fields['DS'])
		);
	endforeach;
	return array( "result" => true, "source" => $arr );
}

function getUnidades(){
	$arr = array();
	
	$result = CONN::get()->execute("
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

function fUnidade( $parameters ) {
	$out = array();
	$frm = null;

	if ( isset($parameters["frm"]) ):
		$frm = $parameters["frm"];
	endif;
	$op = isset($parameters["op"]) ? $parameters["op"] : "";

	//LEITURA DE SAIDA.
	//ATUALIZACAO DE SAIDA
	if ( $op == "UPDATE" ):
		$id = $frm["id"];
		
		$arr = array();
		//INSERT DE NOVO COMUNICADO
		if ( !is_null($id) && is_numeric($id) ):
			$arr = array(
				fReturnStringNull($frm["idade"]),
				fReturnStringNull($frm["fg_ativa"]),
				$id
			);
			CONN::get()->execute("
				UPDATE TAB_UNIDADE SET
					IDADE = ?,
					FG_ATIVA = ?
				WHERE ID = ?
			",$arr);
		endif;
		$out["success"] = true;

	//GET UNIDADE
	else:
    $result = CONN::get()->execute("SELECT * FROM TAB_UNIDADE WHERE ID = ?", array( $parameters["id"] ) );
    if (!$result->EOF):
      $out["success"] = true;
      $out["unidade"] = array(
        "id" => $result->fields['ID'],
        "ds" => $result->fields['DS'],
        "idade" => $result->fields['IDADE'],
        "fg_edit" => $result->fields['FG_EDIT'],
        "fg_ativa" => $result->fields['FG_ATIVA']
      );
    endif;
	endif;
	return $out;
}

function getUFs(){
	$arr = array();
	
	
	$result = CONN::get()->execute("
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
