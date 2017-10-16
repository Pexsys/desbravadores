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
			 WHERE YEAR(cd.DH) = ? $where 
			ORDER BY cd.SQ DESC
	",$aWhere);
}

function fGetMembros(){
	$arr = array();
	fConnDB();
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("
		SELECT o.ID_CAD_PESSOA, a.NM
		  FROM CAD_OCORRENCIA o
	INNER JOIN CON_ATIVOS a ON (a.ID = o.ID_CAD_PESSOA)
		 WHERE YEAR(o.DH) = YEAR(NOW()) 
		   AND o.FG_PEND = ?
	  ORDER BY a.NM
	", array("N") );
	foreach($result as $l => $fields):
		$id = str_pad($fields['ID_CAD_PESSOA'], $qtdZeros, "0", STR_PAD_LEFT);
		$arr["nomes"][] = array(
			"value" => $fields['ID_CAD_PESSOA'],
			"label" => "$id ".($fields['NM'])
		);
	endforeach;
	return $arr;
}

function fOcorrencia( $parameters ) {
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
				fReturnStringNull(trim($frm["txt"])),
				fReturnStringNull(trim($frm["cd"])),
				fReturnStringNull(trim($frm["tp"])),
				$frm["id_pessoa"],
				fReturnStringNull($fg_pend),
				$id
			);
			$GLOBALS['conn']->Execute("
				UPDATE CAD_OCORRENCIA SET
					DH = ?,
					TXT = ?,
					CD = ?,
					TP = ?,
					ID_CAD_PESSOA = ?,
					FG_PEND = ?
				WHERE ID = ?
			",$arr);
		else:
			$arr = array(
				fStrToDate($frm["dh"]),
				fReturnStringNull(trim($frm["txt"])),
				fReturnStringNull(trim($frm["cd"])),
				fReturnStringNull(trim($frm["tp"])),
				$frm["id_pessoa"],		
				fReturnStringNull($fg_pend),
				$userID
			);
			$GLOBALS['conn']->Execute("
				INSERT INTO CAD_OCORRENCIA(
					DH,
					TXT,
					CD,
					TP,
					ID_CAD_PESSOA,
					FG_PEND,
					ID_USUARIO_INS
				) VALUES (?,?,?,?,?,?,?)
			",$arr);
			$id = $GLOBALS['conn']->Insert_ID();
		endif;
		
		//GRAVACAO DEFINITIVA PARA O RESPONSAVEL, ENVIO POR EMAIL
		if ($fg_pend == "N"):
			$GLOBALS['conn']->Execute("
				INSERT INTO LOG_MENSAGEM (ID_ORIGEM, TP, ID_USUARIO, EMAIL, DH_GERA )
				SELECT $id, 'O',  cu.ID_USUARIO, cr.EMAIL_RESP, NOW()
				  FROM CON_ATIVOS ca
			INNER JOIN CAD_RESP cr ON (cr.ID = ca.ID_RESP)
			INNER JOIN CON_ATIVOS cab ON (REPLACE(REPLACE(cab.NR_CPF,'.',''),'-','') = REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-',''))
			INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = cab.ID)
				 WHERE ca.ID = ?
					
				UNION
					
				SELECT $id, 'O', cu.ID_USUARIO, cr.EMAIL_RESP, NOW()
				FROM CON_ATIVOS ca
				INNER JOIN CAD_RESP cr ON (cr.ID = ca.ID_RESP)
				INNER JOIN CAD_USUARIOS cu ON (cu.CD_USUARIO = REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-',''))
				WHERE NOT EXISTS (SELECT 1 FROM CON_ATIVOS WHERE REPLACE(REPLACE(NR_CPF,'.',''),'-','') = REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-',''))
				  AND ca.ID = ?
			", array( $frm["id_pessoa"], $frm["id_pessoa"] ) );
		
			sendOcorrenciaByID($id);	
		endif;
		
		$out["id"] = $id;
		$out["so"] = $fg_pend;
		$out["success"] = true;

	//EXCLUSAO DE SAIDA
	elseif ( $op == "DELETE" ):
		$GLOBALS['conn']->Execute("DELETE FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = ?", Array( $parameters["id"], "O" ) );
		$GLOBALS['conn']->Execute("DELETE FROM CAD_OCORRENCIA WHERE ID = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	//GET SAIDA
	else:
		if ( $parameters["id"] == "Novo" ):
			$result = $GLOBALS['conn']->Execute("SELECT YEAR(NOW()) AS ANO, COUNT(*)+1 AS CD FROM CAD_OCORRENCIA WHERE YEAR(DH) = YEAR(NOW())" );
			$out["success"] = true;
			$out["ocorrencia"] = array(
				"id" => $parameters["id"],
				"fg_pend" => "S",
				"cd" => ( $result->fields['ANO']."-".str_pad($result->fields['CD'], 2, "0", STR_PAD_LEFT))
			);
		else:
			$result = $GLOBALS['conn']->Execute("
				SELECT co.*, ca.NM, cu.DS_USUARIO
				  FROM CAD_OCORRENCIA co
			INNER JOIN CON_ATIVOS ca ON (ca.id = co.id_cad_pessoa)
			INNER JOIN CAD_USUARIOS cu ON (cu.ID_USUARIO = co.ID_USUARIO_INS)
				 WHERE co.ID = ?
			", array( $parameters["id"] ) );
			if (!$result->EOF):
				$out["success"] = true;
				$out["ocorrencia"] = array(
					"id"		=> $result->fields['ID'],
					"cd"		=> ($result->fields['CD']),
					"tp"		=> ($result->fields['TP']),
					"id_pessoa"	=> $result->fields['ID_CAD_PESSOA'],
					"dh"		=> strtotime($result->fields['DH'])."000",
					"txt"		=> (trim($result->fields['TXT'])),
					"owner"		=> (trim($result->fields['DS_USUARIO'])),
					"fg_pend"	=> $result->fields['FG_PEND']
				);
				
			endif;
			
		endif;
		
		if ( !isset($parameters["nomes"]) ):
			$out["nomes"][] = array(
					"id_pessoa" => "",
					"nm" => "(NENHUM)"
			);
			$qtdZeros = zeroSizeID();
			$result = $GLOBALS['conn']->Execute("
				  SELECT DISTINCT at.NM, at.ID
					FROM APR_HISTORICO ah
			  INNER JOIN CON_ATIVOS at ON (at.ID = ah.ID_CAD_PESSOA)
			  INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
				   WHERE at.IDADE_HOJE < 18
					 AND ah.DT_CONCLUSAO IS NULL 
				     AND ta.CD_ITEM_INTERNO LIKE '$like%'
				ORDER BY at.NM
			");
			foreach ($result as $r => $f):
				$id = str_pad($f['ID'], $qtdZeros, "0", STR_PAD_LEFT);
				$out["nomes"][] = array(
						"id_pessoa" => $id,
						"nm" => "$id ".($f['NM'])
				);
			endforeach;
		endif;
		
	endif;
	return $out;
}

function getListaDiario( $parameters ){
	$arr = array();
	fConnDB();
	
	$result = getQueryByFilter( $parameters );

	foreach ($result as $k => $fields):
		$dsReq = (!is_null($fields['CD_AREA']) ? $fields['CD_AREA']."-" : "") . 
				substr($fields['CD_REQ_INTERNO'],-2) . 
				 (!is_null($fields['DS']) ? " ".substr($fields['DS'],0,35) : "");

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

function fSetRead( $parameters ){
	session_start();
	$comunicadoID = $parameters["id"];
	$usuarioID = $_SESSION['USER']['id_usuario'];
	$usuarioCD = $_SESSION['USER']['cd_usuario'];
	
	fConnDB();
	
	//ATUALIZA USUARIO ATUAL
	$GLOBALS['conn']->Execute("
		UPDATE LOG_MENSAGEM SET
			DH_READ = NOW()
		WHERE ID_USUARIO = ?
		  AND ID_ORIGEM = ?
		  AND TP = ?
	", array($usuarioID,$comunicadoID,"O"));
	
	//VERIFICA SE USUARIO ATUAL EH RESPONSAVEL POR OUTRO.
	$result = $GLOBALS['conn']->Execute("
		UPDATE LOG_MENSAGEM SET
			  DH_READ = NOW()
		WHERE ID_USUARIO IN (
							SELECT cu.ID_USUARIO
							FROM CAD_RESP cr
							INNER JOIN CON_ATIVOS ca ON (ca.ID_RESP = cr.ID)
							INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID)
							WHERE REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-','') = ?		
							)
		  AND DH_READ IS NULL
		  AND ID_ORIGEM = ?
		  AND TP = ?
	", array($usuarioCD,$comunicadoID,"O"));
	
	return array( "result" => true );
}
?>