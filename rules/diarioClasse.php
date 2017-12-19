<?php
@require_once("../include/functions.php");
responseMethod();

function getQueryByFilter( $parameters ) {
	session_start();
	$usuarioID = $_SESSION['USER']['id_usuario'];

	$aWhere = array(date("Y"));
	$where = "";
	
	return $GLOBALS['conn']->Execute("
			SELECT cd.ID, cd.SQ, ta.DS_ITEM, taa.CD AS CD_AREA, taa.DS AS DS_AREA, tap.CD_REQ_INTERNO, tap.DS, cd.DH, cd.FG_PEND
			  FROM CAD_DIARIO cd
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = cd.ID_TAB_APREND)
		 LEFT JOIN TAB_APR_ITEM tap ON (tap.ID = cd.ID_TAB_APR_ITEM)
		 LEFT JOIN TAB_APR_AREA taa ON (taa.ID = tap.ID_TAB_APR_AREA)
			 WHERE YEAR(cd.DH) >= ? $where 
			ORDER BY cd.SQ DESC
	",$aWhere);
}

function fRegistro( $parameters ) {
	session_start();
	fConnDB();
	
	$userID = $_SESSION['USER']['id_usuario'];
	$membroID = $_SESSION['USER']['id_cad_pessoa'];
	$out = array();
	$frm = null;
	
	$like = "";
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID = ?
	", array($membroID) );
	$cargo = $result->fields['CD_CARGO'];
	if (fStrStartWith($cargo,"2-07")):
		$cargo = $result->fields['CD_CARGO2'];
	endif;
	if (empty($cargo)):
		return $arr;
	endif;
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$like = "01-".substr($cargo,-2);
	endif;

	if ( isset($parameters["frm"]) ):
		$frm = $parameters["frm"];
	endif;
	$op = isset($parameters["op"]) ? $parameters["op"] : "";

	if ( $op == "UPDATE" ):
		$fg_pend = $frm["fg_pend"];
		$id = $frm["id"];
		
		$arr = array();
		//INSERT DE NOVA OCORRENCIA
		if ( !is_null($id) && is_numeric($id) ):
			$arr = array(
				fStrToDate($frm["dh"]),
				fReturnStringNull($fg_pend),
				fReturnStringNull(trim($frm["id_classe"])),
				fReturnStringNull(trim($frm["id_req"])),
				fReturnStringNull(trim($frm["id_ref"])),
				$frm["sq"],
				fReturnStringNull(trim($frm["txt"])),
				$id
			);
			$GLOBALS['conn']->Execute("
				UPDATE CAD_DIARIO SET
					DH = ?,
					FG_PEND = ?,
					ID_TAB_APREND = ?,
					ID_TAB_APR_ITEM = ?,
					ID_TAB_APR_ITEM_SEL = ?,
					SQ = ?,
					TXT = ?
				WHERE ID = ?
			",$arr);
		else:
			$arr = array(
				fStrToDate($frm["dh"]),
				fReturnStringNull($fg_pend),
				fReturnStringNull(trim($frm["id_classe"])),
				fReturnStringNull(trim($frm["id_req"])),
				fReturnStringNull(trim($frm["id_ref"])),
				$userID,
				$frm["sq"],
				fReturnStringNull(trim($frm["txt"]))
			);
			$GLOBALS['conn']->Execute("
				INSERT INTO CAD_DIARIO(
					DH,
					FG_PEND,
					ID_TAB_APREND,
					ID_TAB_APR_ITEM,
					ID_TAB_APR_ITEM_SEL,
					ID_USUARIO_INS,
					SQ,
					TXT
				) VALUES (?,?,?,?,?,?,?,?)
			",$arr);
			$id = $GLOBALS['conn']->Insert_ID();
		endif;
		
		$out["id"] = $id;
		$out["so"] = $fg_pend;
		$out["success"] = true;

	//EXCLUSAO DE SAIDA
	elseif ( $op == "DELETE" ):
		$GLOBALS['conn']->Execute("DELETE FROM CAD_DIARIO WHERE ID = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	//GET SAIDA
	else:

		if ( $parameters["id"] == "Novo" ):
			$out["success"] = true;
			$out["diario"] = array( "fg_pend" => "S" );
		else:
			$result = $GLOBALS['conn']->Execute("
				SELECT *
				  FROM CAD_DIARIO cd
			INNER JOIN CAD_USUARIOS cu ON (cu.ID_USUARIO = cd.ID_USUARIO_INS)
				 WHERE cd.ID = ?
			", array( $parameters["id"] ) );
			if (!$result->EOF):
				$out["success"] = true;
				$out["diario"] = array(
					"id"			=> $result->fields['ID'],
					"id_classe"		=> $result->fields['ID_TAB_APREND'],
					"id_req"		=> $result->fields['ID_TAB_APR_ITEM'],
					"id_ref"		=> $result->fields['ID_TAB_APR_ITEM_SEL'],
					"sq"			=> $result->fields['SQ'],
					"dh"			=> strtotime($result->fields['DH'])."000",
					"txt"			=> trim($result->fields['TXT']),
					"fg_pend"		=> $result->fields['FG_PEND']
				);
				$out["req"] = fGetReq( $result->fields['ID_TAB_APREND'] );
				$out["ref"] = fGetRefs( $result->fields['ID_TAB_APR_ITEM'] );
			endif;
			
		endif;

		$rc = $GLOBALS['conn']->Execute("
			SELECT ID, DS_ITEM
				FROM TAB_APRENDIZADO
				WHERE CD_ITEM_INTERNO LIKE '$like%'
				AND TP_ITEM = 'CL'
				AND TP_PARA = 'DL'
			ORDER BY CD_ITEM_INTERNO
		");
		foreach ($rc as $r => $f):
			$out["classe"][] = array(
				"value" => $f['ID'],
				"label" => $f['DS_ITEM']
			);
		endforeach;

	endif;
	return $out;
}

function fGetCompl( $parameters ){
	fConnDB();

	$result = $GLOBALS['conn']->Execute("
		SELECT MAX(SQ)+1 AS SQ 
		FROM CAD_DIARIO 
		WHERE ID_TAB_APREND = ?
		AND YEAR(DH) = YEAR(NOW())
	", array($parameters["id_classe"]) );
	$sq = ( !is_null($result->fields["SQ"]) ? $result->fields["SQ"] : 1);

	return array( "sq" => $sq, "req" => fGetReq( $parameters["id_classe"] ) );
}

function fGetReq( $classeID ){
	$arr = array();
	$result = $result = $GLOBALS['conn']->Execute("
		   SELECT tap.ID, taa.SEQ, taa.CD AS CD_AREA, taa.DS AS DS_AREA, tap.CD_REQ_INTERNO, tap.DS, tap.QT_MIN
			 FROM TAB_APR_ITEM tap 
		LEFT JOIN TAB_APR_AREA taa ON (taa.ID = tap.ID_TAB_APR_AREA)
			WHERE tap.ID_TAB_APREND = ? 
		 ORDER BY taa.SEQ, tap.CD_REQ_INTERNO
	", array( $classeID ) );

	foreach ($result as $k => $fields):
		$dsReq = (!is_null($fields['CD_AREA']) ? $fields['CD_AREA']."-" : "") . 
				substr($fields['CD_REQ_INTERNO'],-2) . 
				 (!is_null($fields['DS']) ? " ".$fields['DS'] : "");

		$arr[] = array(
			"id" => $fields['ID'],
			"ds" => $dsReq,
			"tp" => (!is_null($fields['QT_MIN']) ? "E" : "")
		);
	endforeach;
	return $arr;
}

function fGetRef( $parameters ){
	fConnDB();
	return fGetRefByID( $parameters["id_req"] );
}

function fGetRefByID( $refID ){
	$arr = array();
	$result = $result = $GLOBALS['conn']->Execute("
		SELECT tais.ID, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
		FROM TAB_APR_ITEM_SEL tais
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = tais.ID_REF)
		WHERE tais.ID_TAB_APR_ITEM = ? 
		ORDER BY ta.CD_AREA_INTERNO, ta.DS_ITEM
	", array( $refID ) );

	foreach ($result as $k => $fields):
		$arr[] = array( 
			"id"	=> $fields['ID'],
			"ds"	=> $fields['DS_ITEM'],
			"sb"	=> $fields['CD_ITEM_INTERNO']
		);
	endforeach;
	return $arr;
}

function getListaDiario( $parameters ){
	$arr = array();
	fConnDB();
	
	$result = getQueryByFilter( $parameters );

	foreach ($result as $k => $fields):
		$dsReq = (!is_null($fields['CD_AREA']) ? $fields['CD_AREA']."-" : "") . 
				substr($fields['CD_REQ_INTERNO'],-2) . 
				 (!is_null($fields['DS']) ? " ".substr($fields['DS'],0,60) : "");

		$arr[] = array(
			"id" => $fields['ID'],
			"sq" => $fields['SQ'],
			"cl" => $fields['DS_ITEM'],
			"rq" => $dsReq,
			"st" => $fields['FG_PEND'],
			"so" => $fields['FG_PEND'],
			"dh" => strtotime($fields['DH'])
		);
	endforeach;
	return array( "result" => true, "diario" => $arr );
}
?>