<?php
@require_once("../include/functions.php");
responseMethod();

function fComunicado( $parameters ) {
	$out = array();
	$frm = null;

	if ( isset($parameters["frm"]) ):
		$frm = $parameters["frm"];
	endif;
	$op = isset($parameters["op"]) ? $parameters["op"] : "";

	fConnDB();

	//LEITURA DE SAIDA.
	//ATUALIZACAO DE SAIDA
	if ( $op == "UPDATE" ):
		$fg_pend = $frm["fg_pend"];
		$id = $frm["id"];
		
		$arr = array();
		//INSERT DE NOVO COMUNICADO
		if ( !is_null($id) && is_numeric($id) ):
			$arr = array(
				fStrToDate($frm["dh"]),
				fReturnStringNull(trim($frm["txt"])),
				fReturnStringNull(trim($frm["cd"])),
				fReturnStringNull($fg_pend),
				$id
			);
			$GLOBALS['conn']->Execute("
				UPDATE CAD_COMUNICADO SET
					DH = ?,
					TXT = ?,
					CD = ?,
					FG_PEND = ?
				WHERE ID = ?
			",$arr);
		else:
			$arr = array(
				fStrToDate($frm["dh"]),
				fReturnStringNull(trim($frm["txt"])),
				fReturnStringNull(trim($frm["cd"])),
				fReturnStringNull($fg_pend)
			);
			$GLOBALS['conn']->Execute("
				INSERT INTO CAD_COMUNICADO(
					DH,
					TXT,
					CD,
					FG_PEND
				) VALUES (?,?,?,?)
			",$arr);
			$id = $GLOBALS['conn']->Insert_ID();
		endif;
		
		//GRAVACAO DEFINITIVA, ENVIO POR EMAIL
		if ($fg_pend == "N"):
			$GLOBALS['conn']->Execute("
				INSERT INTO LOG_MENSAGEM (ID_ORIGEM, TP, ID_USUARIO, EMAIL, DH_GERA)
				SELECT $id, 'C',  cu.ID_USUARIO, ca.EMAIL, NOW() 
				  FROM CON_ATIVOS ca
		    INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID) 
				
				UNION
				
				SELECT $id, 'C', cu.ID_USUARIO, cr.EMAIL_RESP AS EMAIL, NOW()
				FROM CAD_RESP cr
				INNER JOIN CON_ATIVOS ca ON (ca.ID_RESP = cr.ID)
				INNER JOIN CAD_USUARIOS cu ON (cu.CD_USUARIO = REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-',''))
				WHERE NOT EXISTS (SELECT 1 FROM CON_ATIVOS WHERE REPLACE(REPLACE(NR_CPF,'.',''),'-','') = REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-',''))
			");
		endif;
		
		$out["id"] = $id;
		$out["so"] = $fg_pend;
		$out["success"] = true;

	//EXCLUSAO DE SAIDA
	elseif ( $op == "DELETE" ):
		$GLOBALS['conn']->Execute("DELETE FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = ?", Array( $parameters["id"], "C" ) );
		$GLOBALS['conn']->Execute("DELETE FROM CAD_COMUNICADO WHERE ID = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	//GET SAIDA
	else:
		if ( $parameters["id"] == "Novo" ):
			$result = $GLOBALS['conn']->Execute("SELECT YEAR(NOW()) AS ANO, COUNT(*)+1 AS CD FROM CAD_COMUNICADO WHERE YEAR(DH) = YEAR(NOW())" );
			$out["success"] = true;
			$out["comunicado"] = array(
				"id" => $parameters["id"],
				"fg_pend" => "S",
				"cd" => utf8_encode( $result->fields['ANO']."-".str_pad($result->fields['CD'], 2, "0", STR_PAD_LEFT))
			);
			
		else:
			$result = $GLOBALS['conn']->Execute("SELECT * FROM CAD_COMUNICADO WHERE ID = ?", array( $parameters["id"] ) );
			if (!$result->EOF):
				$out["success"] = true;
				$out["comunicado"] = array(
					"id"		=> $result->fields['ID'],
					"cd"		=> utf8_encode($result->fields['CD']),
					"dh"		=> strtotime($result->fields['DH'])."000",
					"txt"		=> utf8_encode(trim($result->fields['TXT'])),
					"fg_pend"	=> $result->fields['FG_PEND']
				);
			endif;
		endif;
	endif;
	return $out;
}

function getComunicados( $parameters ){
	session_start();
	$usuarioID = $_SESSION['USER']['id_usuario'];
	
	$arr = array();
	fConnDB();
	
	if ($parameters["filter"] == "N"):
		$result = $GLOBALS['conn']->Execute("
			SELECT cc.ID, cc.CD, cc.DH, cc.FG_PEND, lc.DH_READ
			  FROM CAD_COMUNICADO cc
		INNER JOIN LOG_MENSAGEM lc ON (lc.ID_ORIGEM = cc.ID AND lc.TP = 'C')
			 WHERE YEAR(cc.DH) = YEAR(NOW())
			   AND cc.FG_PEND = 'N'
			   AND lc.ID_USUARIO = ?
		  ORDER BY ID DESC
		", array( $usuarioID ) );
	else:
		$result = $GLOBALS['conn']->Execute("SELECT ID, CD, DH, FG_PEND
				   FROM CAD_COMUNICADO 
				  WHERE YEAR(DH) = YEAR(NOW())
			   ORDER BY ID DESC");
	endif;

	foreach ($result as $k => $fields):
		if ($parameters["filter"] == "N"):
			$arr[] = array(
				"id" => $fields['ID'],
				"cd" => $fields['CD'],
				"st" => (is_null($fields['DH_READ']) ? "S" : "N"),
				"dh" => strtotime($fields['DH'])
			);
		else:
			$arr[] = array(
				"id" => $fields['ID'],
				"cd" => $fields['CD'],
				"st" => $fields['FG_PEND'],
				"so" => $fields['FG_PEND'],
				"dh" => strtotime($fields['DH'])
			);
		endif;
	endforeach;
	return array( "result" => true, "comunic" => $arr );
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
	", array($usuarioID,$comunicadoID,"C"));
	
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
	", array($usuarioCD,$comunicadoID,"C"));
	
	return array( "result" => true );
}
?>