<?php
@require_once("../include/functions.php");
@require_once("sendmailOcorrencias.php");
responseMethod();

function getQueryByFilter( $parameters ) {
	session_start();
	$usuarioID = $_SESSION['USER']['id'];
	
	if ($parameters["filter"] == "N"):
		return CONN::get()->Execute("
				SELECT o.ID, a.NM, o.TP, o.CD, o.DH, o.FG_PEND, l.DH_READ
				  FROM CAD_OCORRENCIA o
			INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = o.ID_CAD_PESSOA)
			INNER JOIN LOG_MENSAGEM l ON (l.ID_ORIGEM = o.ID AND l.TP = 'O')
				 WHERE YEAR(o.DH) = YEAR(NOW())
				   AND o.FG_PEND = 'N'
				   AND l.ID_CAD_USUARIO = ?
			  ORDER BY o.ID DESC
		", array( $usuarioID ) );
	
	else:
		$aWhere = array( date("Y") );
		$where = "";
		if ( isset($parameters["filters"]) ):
			$keyAnt = "";
			foreach ($parameters["filters"] as $key => $v):
				$not = false;
				if ( isset($parameters["filters"][$key]["fg"]) ):
					$not = strtolower($parameters["filters"][$key]["fg"]) == "true";
				endif;
				$notStr = ( $not ? "NOT " : "" );
				if ( $key == "X" ):
					$where .= " AND a.TP_SEXO ".$notStr."IN";
				elseif ( $key == "U" ):
					$where .= " AND a.ID_UNIDADE ".$notStr."IN";
				elseif ( $key == "TO" ):
					$where .= " AND o.TP ".$notStr."IN";
				else:
					$where .= " AND";
				endif;
				
				$prim = true;
				$where .= " (";
				if ( is_array( $parameters["filters"][$key]["vl"] ) ):
					foreach ($parameters["filters"][$key]["vl"] as $value):
						$aWhere[] = $value;
						$where .= (!$prim ? "," : "" )."?";
						$prim = false;
					endforeach;
				else:
					$aWhere[] = "$notStr"."NULL";
					$where .= "?";
				endif;
				$where .= ")";
			endforeach;
		endif;
		
		return CONN::get()->Execute("
				SELECT o.ID, a.NM, o.TP, o.CD, o.DH, o.FG_PEND
				  FROM CAD_OCORRENCIA o
			INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = o.ID_CAD_PESSOA)
				 WHERE YEAR(o.DH) = ? $where 
			  ORDER BY o.ID DESC
		",$aWhere);
	endif;
}

function fGetMembros(){
	$arr = array();
	
	$qtdZeros = zeroSizeID();
	$result = CONN::get()->Execute("
		SELECT o.ID_CAD_PESSOA, a.NM
		  FROM CAD_OCORRENCIA o
	INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = o.ID_CAD_PESSOA)
		 WHERE YEAR(o.DH) = YEAR(NOW()) 
		   AND o.FG_PEND = ?
	  ORDER BY a.NM
	", array("N") );
	foreach($result as $l => $fields):
		$id = fStrZero($fields['ID_CAD_PESSOA'], $qtdZeros);
		$arr["nomes"][] = array(
			"id" => $fields['ID_CAD_PESSOA'],
			"ds" => $fields['NM'],
			"sb" => $id
		);
	endforeach;
	return $arr;
}

function fOcorrencia( $parameters ) {
	session_start();
	
	$userID = $_SESSION['USER']['id'];
	$membroID = $_SESSION['USER']['id_cad_pessoa'];
	$out = array();
	$frm = null;
	
	$like = "";
	$result = CONN::get()->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID_CAD_PESSOA = ?
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
			CONN::get()->Execute("
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
			CONN::get()->Execute("
				INSERT INTO CAD_OCORRENCIA(
					DH,
					TXT,
					CD,
					TP,
					ID_CAD_PESSOA,
					FG_PEND,
					ID_CAD_USUARIO
				) VALUES (?,?,?,?,?,?,?)
			",$arr);
			$id = CONN::get()->Insert_ID();
		endif;
		
		//GRAVACAO DEFINITIVA PARA O RESPONSAVEL, ENVIO POR EMAIL
		if ($fg_pend == "N"):
			CONN::get()->Execute("
				INSERT INTO LOG_MENSAGEM (ID_ORIGEM, TP, ID_CAD_USUARIO, EMAIL, DH_GERA)
				SELECT $id, 'O', cu.ID, ca.EMAIL, NOW() 
				  FROM CON_ATIVOS ca
		    INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA) 
 				 WHERE ca.ID_CAD_PESSOA = ? 
				
				UNION
				
				SELECT $id, 'O', cu.ID, cr.EMAIL, NOW() 
				FROM CON_RESP_LEGAL cr 
				INNER JOIN CON_ATIVOS ca ON (ca.ID_PESSOA_RESP = cr.ID) 
				INNER JOIN CAD_USUARIO cu ON (cu.CD_USUARIO = cr.NR_CPF) 
				WHERE NOT EXISTS (SELECT 1 FROM CON_ATIVOS WHERE NR_CPF = cr.NR_CPF)
				WHERE ca.ID_CAD_PESSOA = ? 
			", array( $frm["id_pessoa"], $frm["id_pessoa"] ) );
		
			sendOcorrenciaByID($id);
		endif;
		
		$out["id"] = $id;
		$out["so"] = $fg_pend;
		$out["success"] = true;

	//EXCLUSAO DE SAIDA
	elseif ( $op == "DELETE" ):
		CONN::get()->Execute("DELETE FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = ?", Array( $parameters["id"], "O" ) );
		CONN::get()->Execute("DELETE FROM CAD_OCORRENCIA WHERE ID = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	//GET SAIDA
	else:
		if ( $parameters["id"] == "Novo" ):
			$result = CONN::get()->Execute("SELECT YEAR(NOW()) AS ANO, COUNT(*)+1 AS CD FROM CAD_OCORRENCIA WHERE YEAR(DH) = YEAR(NOW())" );
			$out["success"] = true;
			$out["ocorrencia"] = array(
				"id" => $parameters["id"],
				"fg_pend" => "S",
				"cd" => $result->fields['ANO']."-".fStrZero($result->fields['CD'], 2)
			);
		else:
			$result = CONN::get()->Execute("
				SELECT co.*, ca.NM, cu.DS_USUARIO
				  FROM CAD_OCORRENCIA co
			INNER JOIN CON_ATIVOS ca ON (ca.id_cad_pessoa = co.id_cad_pessoa)
			INNER JOIN CAD_USUARIO cu ON (cu.ID = co.ID_CAD_USUARIO)
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
			$result = CONN::get()->Execute("
				  SELECT DISTINCT ca.NM, ca.ID
					FROM APR_HISTORICO ah
			  INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
			  INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
				   WHERE ca.IDADE_HOJE < 18
					 AND ah.DT_CONCLUSAO IS NULL 
				     AND ta.CD_ITEM_INTERNO LIKE '$like%'
				ORDER BY ca.NM
			");
			foreach ($result as $r => $f):
				$id = fStrZero($f['ID'], $qtdZeros);
				$out["nomes"][] = array(
						"id_pessoa" => $id,
						"nm" => $f['NM'],
						"sb" => $id
				);
			endforeach;
		endif;
		
	endif;
	return $out;
}

function getOcorrencias( $parameters ){
	$arr = array();
	
	
	$result = getQueryByFilter( $parameters );

	foreach ($result as $k => $fields):
		if ($parameters["filter"] == "N"):
			$arr[] = array(
				"id" => $fields['ID'],
				"cd" => $fields['CD'],
				"tp" => $fields['TP'],
				"nm" => ($fields['NM']),
				"st" => (is_null($fields['DH_READ']) ? "S" : "N"),
				"dh" => strtotime($fields['DH'])
			);
		else:
			$arr[] = array(
				"id" => $fields['ID'],
				"cd" => $fields['CD'],
				"tp" => $fields['TP'],
				"nm" => ($fields['NM']),
				"st" => $fields['FG_PEND'],
				"so" => $fields['FG_PEND'],
				"dh" => strtotime($fields['DH'])
			);
		endif;
	endforeach;
	return array( "result" => true, "ocorr" => $arr );
}

function fSetRead( $parameters ){
	session_start();
	$comunicadoID = $parameters["id"];
	$usuarioID = $_SESSION['USER']['id'];
	$usuarioCD = $_SESSION['USER']['cd_usuario'];
	
	
	
	//ATUALIZA USUARIO ATUAL
	CONN::get()->Execute("
		UPDATE LOG_MENSAGEM SET
			DH_READ = NOW()
		WHERE ID_CAD_USUARIO = ?
		  AND ID_ORIGEM = ?
		  AND TP = ?
	", array($usuarioID,$comunicadoID,"O"));
	
	//VERIFICA SE USUARIO ATUAL EH RESPONSAVEL POR OUTRO.
	$result = CONN::get()->Execute("
		UPDATE LOG_MENSAGEM SET
			  DH_READ = NOW()
		WHERE ID_CAD_USUARIO IN (
							SELECT cu.ID
							FROM CON_RESP_LEGAL cr
							INNER JOIN CON_ATIVOS ca ON (ca.ID_PESSOA_RESP = cr.ID)
							INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
							WHERE cr.NR_CPF = ?		
							)
		  AND DH_READ IS NULL
		  AND ID_ORIGEM = ?
		  AND TP = ?
	", array($usuarioCD,$comunicadoID,"O"));
	
	return array( "result" => true );
}
?>