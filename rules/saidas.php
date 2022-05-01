<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	$where = "";
	$aWhere = array();

	$strQuery = "";
	if ( !isset($parameters["filters"]) ):
		$strQuery .= "
		SELECT  p.NM,
				p.ID_CAD_PESSOA,
				m.ID AS ID_CAD_MEMBRO,
				esm.ID AS ID_EVE_MEMBRO,
				esm.ID_EVE_SAIDA,
				p.IDADE_HOJE,
				YEAR(es.DH_R)-YEAR(p.DT_NASC) - IF(DATE_FORMAT(p.DT_NASC,'%m%d')>DATE_FORMAT(es.DH_R,'%m%d'),1,0) AS IDADE_EVENTO_FIM,
				esm.FG_AUTORIZ
			FROM EVE_SAIDA es
		INNER JOIN CAD_ATIVOS a ON (a.NR_ANO = YEAR(es.DH_S))
		INNER JOIN CAD_MEMBRO m ON (m.ID = a.ID_CAD_MEMBRO)
		INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
		LEFT JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = a.ID_CAD_MEMBRO AND esm.ID_EVE_SAIDA = es.ID)
		WHERE es.ID = ?

		UNION
		";
		if ($parameters["id"] == "Novo"):
			$aWhere[] = null;
		else:
			$aWhere[] = $parameters["id"];

			$where .= " AND NOT EXISTS (SELECT  1
									FROM EVE_SAIDA es
								INNER JOIN CAD_ATIVOS a ON (a.NR_ANO = YEAR(es.DH_S))
								INNER JOIN CAD_MEMBRO m ON (m.ID = a.ID_CAD_MEMBRO)
								INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
								LEFT JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = a.ID_CAD_MEMBRO AND esm.ID_EVE_SAIDA = es.ID)
								WHERE es.ID = ?)";
			$aWhere[] = $parameters["id"];
		endif;
	endif;

	$strQuery .= "SELECT  ca.NM,
				ca.ID_CAD_PESSOA,
				ca.ID_CAD_MEMBRO,
				esm.ID AS ID_EVE_MEMBRO,
				es.ID AS ID_EVE_SAIDA,
				ca.IDADE_HOJE,
				YEAR(es.DH_R)-YEAR(ca.DT_NASC) - IF(DATE_FORMAT(ca.DT_NASC,'%m%d')>DATE_FORMAT(es.DH_R,'%m%d'),1,0) AS IDADE_EVENTO_FIM,
				esm.FG_AUTORIZ
		FROM CON_ATIVOS ca
		LEFT JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.ID_EVE_SAIDA = ?)
		LEFT JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA)
		WHERE 1=1
	";
	if ($parameters["id"] == "Novo"):
		$aWhere[] = null;
	else:
		$aWhere[] = $parameters["id"];
	endif;

	//if ( isset($parameters["dhr"]) ):
	//	$dhr = fStrToDate($parameters["dhr"]);
	//	$where .= " AND YEAR(DATE('$dhr'))-YEAR(ca.DT_NASC) - IF(DATE_FORMAT(ca.DT_NASC,'%m%d')>DATE_FORMAT(DATE('$dhr'),'%m%d'),1,0) < 18";
	//else:
	//	$where .= " ca.IDADE_HOJE < ?";
	//	$aWhere[] = 18;
	//endif;

	if ( isset($parameters["filters"]) ):
		$keyAnt = "";
		foreach ($parameters["filters"] as $key => $v):
			$not = false;
			if ( isset($parameters["filters"][$key]["fg"]) ):
				$not = strtolower($parameters["filters"][$key]["fg"]) == "true";
			endif;
			$notStr = ( $not ? "NOT " : "" );
			if ( $key == "X" ):
				$where .= " AND ca.tp_sexo ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND ca.id_unidade ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND EXISTS (
							SELECT DISTINCT 1
							FROM TAB_APRENDIZADO ta
							LEFT JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = ta.ID AND ah.DT_CONCLUSAO IS NULL)
							WHERE ta.TP_ITEM = 'CL' AND ah.ID_CAD_PESSOA = ca.ID_CAD_PESSOA AND
							ta.ID ".$notStr."IN";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $parameters["filters"][$key]["vl"] ) ):
				foreach ($parameters["filters"][$key]["vl"] as $value):
					if ( $key == "B" ):
						if ($value == "S"):
							$where .= (!$prim ? " OR " : "") ."ca.DT_BAT IS ". ( $value == "S" && !$not ? "NOT NULL" : "NULL");
						elseif ($value == "N"):
							$where .= (!$prim ? " OR " : "") ."ca.DT_BAT IS ". ( $value == "N" && !$not ? "NULL" : "NOT NULL");
						elseif (fStrStartWith($value,"A")):
							$where .= (!$prim ? " OR " : "") ."YEAR(ca.DT_BAT) ". ( !$not ? " < " : " >= ") . substr($value,1,4);
						else:
							$where .= (!$prim ? " OR " : "") ."YEAR(ca.DT_BAT) ". ( !$not ? " = " : " <> ") . $value;
						endif;
					elseif ( $key == "G" ):
						if ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."ca.CD_FANFARRA IS ".( !$not ? "NOT NULL" : "NULL");
						elseif ( $value == "4" ):
						    $where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-04%'");
						elseif ( $value == "5" ):
						    $where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-07%'");
						elseif ( $value == "6" ):
						    $where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '1-01%'");
						else:
							$where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
						endif;
					elseif ( empty($value) ):
						$aWhere[] = "NULL";
						$where .= (!$prim ? "," : "" )."?";
					else:
						$aWhere[] = $value;
						$where .= (!$prim ? "," : "" )."?";
					endif;
					$prim = false;
				endforeach;
			else:
				$aWhere[] = "$notStr"."NULL";
				$where .= "?";
			endif;
			$where .= ")";
			if ( $key == "C" ):
				$where .= ")";
			endif;
		endforeach;
	endif;
	$strQuery .= " $where ORDER BY 1";

	//echo $strQuery;
	//print_r($aWhere);
	//echo "<br/><br/>";

	return CONN::get()->execute($strQuery, $aWhere);
}

function getNames(){
	$arr = array();

	session_start();
	$usuarioID = $_SESSION['USER']['id'];
	$qtdZeros = zeroSizeID();

	$unidadeID = null;
	$cadMembroID = null;
	$pessoaID = null;
	$cargo = null;
	$pessoaRespID = null;

	//MEMBRO LOGADO
	$result = CONN::get()->execute("
		SELECT cu.ID_CAD_PESSOA, ca.ID_MEMBRO, ca.ID_CAD_MEMBRO, ca.ID_UNIDADE, ca.CD_CARGO, ca.CD_CARGO2, ca.NM, ca.IDADE_HOJE, ca.ID_PESSOA_RESP
		  FROM CON_ATIVOS ca
	INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
	     WHERE cu.ID = ?
	", array( $usuarioID ) );
	if (!$result->EOF):
		$unidadeID = $result->fields["ID_UNIDADE"];
		$cadMembroID = $result->fields["ID_CAD_MEMBRO"];
		$membroID = $result->fields["ID_MEMBRO"];
		$pessoaID = $result->fields["ID_CAD_PESSOA"];
		$membroNM = $result->fields["NM"];
		$pessoaRespID = $result->fields["ID_PESSOA_RESP"];

		$rs = CONN::get()->execute("
			SELECT 1
			FROM EVE_SAIDA_MEMBRO esm
	  INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S')
		   WHERE esm.ID_CAD_MEMBRO = ?
		     AND esm.FG_AUTORIZ = 'S'
		", array($cadMembroID) );
		if ($result->fields["IDADE_HOJE"] < 18 && !$rs->EOF):
			$arr[] = array( "id" => $cadMembroID, "ds" => "<<mim>> - $membroNM", "sb" => fStrZero($membroID, $qtdZeros) );
		endif;

		$cargo = $result->fields['CD_CARGO'];
		if (fStrStartWith($cargo,"2-07")):
			$cargo = $result->fields['CD_CARGO2'];
		endif;
	endif;

	$aQuery = array( "query" => "", "binds" => array() );

	//TRATAMENTO MEMBROS DA MINHA UNIDADE
	$aQuery = getUnionByUnidade( $aQuery, $unidadeID, $cadMembroID );

	//TRATAMENTO MEMBROS QUE ESTAO FAZENDO AS MESMAS CLASSES QUE EU
	$aQuery = getUnionByClasses( $aQuery, $pessoaID, $cadMembroID );

	//TRATAMENTO PARA INSTRUTOR DE CLASSE
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$classe = "01-".substr($cargo,-2);

		$aQuery["query"] .= " UNION
			SELECT DISTINCT ca.NM, ca.ID_CAD_MEMBRO, ca.ID_MEMBRO
			  FROM CON_APR_PESSOA cap
		    INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
	            INNER JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.FG_AUTORIZ = 'S')
	            INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S')
			 WHERE cap.CD_ITEM_INTERNO LIKE '$classe%'
			   AND cap.DT_CONCLUSAO IS NULL
			   AND ca.ID_CAD_MEMBRO <> ?
			   AND ca.IDADE_HOJE < 18";
		$aQuery["binds"][] = $cadMembroID;
	endif;

	//TRATAMENTO PARA ADMINISTRACAO
	if ($cargo == "2-01-00" || $cargo == "2-02-00"):
		$aQuery["query"] .= " UNION
	           SELECT ca.NM, ca.ID_CAD_MEMBRO, ca.ID_MEMBRO
	           FROM CON_ATIVOS ca
	     INNER JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.FG_AUTORIZ = 'S')
	     INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW())
	           WHERE ca.ID_CAD_MEMBRO <> ?
	             AND ca.IDADE_HOJE < 18";
		$aQuery["binds"][] = $cadMembroID;

	//TRATAMENTO PARA INSTRUTORES NAO ESPECIFICOS
	elseif ($cargo == "2-04-00" || $cargo == "2-04-99"):
		$aQuery["query"] .= " UNION
	           SELECT ca.NM, ca.ID_CAD_MEMBRO, ca.ID_MEMBRO
	           FROM CON_ATIVOS ca
	     INNER JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.FG_AUTORIZ = 'S')
	     INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S')
	           WHERE ca.ID_CAD_MEMBRO <> ?
	             AND ca.IDADE_HOJE < 18";
		$aQuery["binds"][] = $cadMembroID;
	endif;

	//TRATAMENTO MEMBROS QUE POSSUAM O MESMO RESPONSAVEL
	if (!is_null($pessoaRespID)):
		$aQuery["query"] .= " UNION
	           SELECT ca.NM, ca.ID_CAD_MEMBRO, ca.ID_MEMBRO
	           FROM CON_ATIVOS ca
	     INNER JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.FG_AUTORIZ = 'S')
	     INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S')
	           WHERE ca.ID_CAD_MEMBRO <> ?
				 AND ca.IDADE_HOJE < 18
				 AND ca.ID_PESSOA_RESP = ?";
		$aQuery["binds"][] = $cadMembroID;
		$aQuery["binds"][] = $pessoaRespID;
	endif;

	//TRATAMENTO MEUS DEPENDENTES, SUAS UNIDADES OU SUAS CLASSES
	$rd = CONN::get()->execute("
		SELECT ca.ID_CAD_PESSOA, ca.ID_UNIDADE
		FROM CAD_USUARIO cu
		INNER JOIN CON_ATIVOS ca ON (ca.ID_PESSOA_RESP = cu.ID_CAD_PESSOA)
		INNER JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.FG_AUTORIZ = 'S')
		INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S')
		WHERE cu.ID = ?
		  AND ca.IDADE_HOJE < 18
	", array($usuarioID) );
	foreach ($rd as $k => $l):
		$aQuery = getUnionByUnidade( $aQuery, $l["ID_UNIDADE"], $cadMembroID );
		$aQuery = getUnionByClasses( $aQuery, $l["ID_CAD_PESSOA"], $cadMembroID );
	endforeach;

	if (!empty($aQuery["query"])):
		//print_r($aQuery);
		$rs = CONN::get()->execute("SELECT * FROM (".substr($aQuery["query"], 7).
			") x WHERE x.ID_CAD_MEMBRO NOT IN (
					SELECT m.ID
					FROM CAD_MEMBRO m
					INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
					INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = p.ID_CAD_PESSOA)
					WHERE
						(
							LENGTH(p.NM)<=5
							OR p.TP_SEXO NOT IN ('M','F')
							OR (p.DT_NASC IS NULL OR LENGTH(p.DT_NASC) = 0)
							OR (p.NR_DOC IS NULL OR LENGTH(p.NR_DOC) < 7 OR INSTR(TRIM(p.NR_DOC),' ') < 3)
							OR ((p.NR_CPF IS NULL OR LENGTH(p.NR_CPF)=0) AND p.NR_CPF_RESP IS NULL)
							OR (p.LOGRADOURO IS NULL OR LENGTH(p.LOGRADOURO) = 0)
							OR (p.NR_LOGR IS NULL OR LENGTH(p.NR_LOGR) = 0)
							OR (p.BAIRRO IS NULL OR LENGTH(p.BAIRRO) = 0)
							OR (p.CIDADE IS NULL OR LENGTH(p.CIDADE) = 0)
							OR (p.UF IS NULL OR LENGTH(p.UF) = 0)
							OR (p.CEP IS NULL OR LENGTH(p.CEP) = 0)
							OR ((p.FONE_RES IS NULL AND p.FONE_CEL IS NULL) OR LENGTH(CONCAT(p.FONE_RES,p.FONE_CEL)) = 0)
							OR (p.IDADE_ANO < 18 AND (p.ID_PESSOA_RESP IS NULL OR (p.NR_DOC_RESP IS NULL OR LENGTH(p.NR_DOC_RESP) < 7 OR INSTR(TRIM(p.NR_DOC_RESP),' ') < 3) OR (p.NR_CPF_RESP IS NULL OR LENGTH(p.NR_CPF_RESP)=0)))
							OR (a.ID_UNIDADE IS NULL OR a.ID_UNIDADE = 0)
							OR (a.CD_CARGO IS NULL OR LENGTH(a.CD_CARGO)<=1)
						)
					)
			ORDER BY 1", $aQuery["binds"] );
		if (!$rs->EOF):
			foreach ($rs as $k => $line):
				$arr[] = array( "id" => $line["ID_CAD_MEMBRO"], "ds" => $line["NM"], "sb" => fStrZero($line["ID_MEMBRO"], $qtdZeros) );
			endforeach;
		endif;
	endif;

	return array( "result" => true, "names" => $arr );
}

function getUnionByUnidade($aQuery, $unidadeID, $cadMembroID){
	if (!is_null($unidadeID) && !is_null($cadMembroID)):
		$aQuery["query"] .=" UNION
		SELECT ca.NM, ca.ID_CAD_MEMBRO, ca.ID_MEMBRO
		FROM CON_ATIVOS ca
      INNER JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.FG_AUTORIZ = 'S')
	  INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S')
		WHERE ca.ID_UNIDADE = ?
		  AND ca.ID_CAD_MEMBRO <> ?
		  AND ca.IDADE_HOJE < 18
		";
		$aQuery["binds"][] = $unidadeID;
		$aQuery["binds"][] = $cadMembroID;
	endif;
	return $aQuery;
}

function getUnionByClasses($aQuery, $pessoaID, $cadMembroID){
	if (!is_null($membroID) && !is_null($cadMembroID)):
		$aQuery["query"] .= " UNION
		SELECT DISTINCT ca.NM, ca.ID_CAD_MEMBRO, ca.ID_MEMBRO
		  FROM CON_APR_PESSOA cap
	INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
    INNER JOIN EVE_SAIDA_MEMBRO esm ON (esm.ID_CAD_MEMBRO = ca.ID_CAD_MEMBRO AND esm.FG_AUTORIZ = 'S')
	INNER JOIN EVE_SAIDA es ON (es.ID = esm.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S')
		 WHERE cap.CD_ITEM_INTERNO IN (SELECT DISTINCT CD_ITEM_INTERNO FROM CON_APR_PESSOA WHERE ID_CAD_PESSOA = ? AND TP_ITEM = 'CL' AND DT_CONCLUSAO IS NULL)
		   AND cap.DT_CONCLUSAO IS NULL
		   AND ca.ID_CAD_MEMBRO <> ?
		   AND ca.IDADE_HOJE < 18";
		$aQuery["binds"][] = $pessoaID;
		$aQuery["binds"][] = $cadMembroID;
	endif;
	return $aQuery;
}

function fSaida( $parameters ) {
	$out = array();
	$particip = array();
	$frm = null;

	if ( isset($parameters["frm"]) ):
		$frm = $parameters["frm"];
		if ( isset($frm["particip"]) && is_array($frm["particip"]) ):
			$particip = $frm["particip"];
		endif;
	endif;
	$op = isset($parameters["op"]) ? $parameters["op"] : "";

	//LEITURA DE SAIDA.
	//ATUALIZACAO DE SAIDA
	if ( $op == "UPDATE" ):
		$arr = array();
		//INSERT DE NOVA SAIDA
		if ( !is_null($frm["id"]) && is_numeric($frm["id"]) ):
			$arr = array(
				fStrToDate($frm["dh_s"]),
				fStrToDate($frm["dh_r"]),
				fReturnStringNull($frm["id_cad_eventos"]),
				fReturnStringNull(trim($frm["ds"])),
				fReturnStringNull(trim($frm["ds_tema"])),
				fReturnStringNull(trim($frm["ds_org"])),
				fReturnStringNull(trim($frm["ds_dest"])),
				fReturnStringNull(trim($frm["ds_orig"])),
				fReturnStringNull(trim($frm["tp_autoriz"])),
				fReturnStringNull(trim($frm["fg_imprimir"])),
				$frm["id"]
			);
			CONN::get()->execute("
				UPDATE EVE_SAIDA SET
					DH_S = ?,
					DH_R = ?,
					ID_CAD_EVENTOS = ?,
					DS = ?,
					DS_TEMA = ?,
					DS_ORG = ?,
					DS_DEST = ?,
					DS_ORIG = ?,
					TP_AUTORIZ = ?,
					FG_IMPRIMIR = ?
				WHERE ID = ?",$arr);

			fSaidaMembro( $frm["id"], $particip );
			$out["id"] = $frm["id"];

		else:
			$arr = array(
				fStrToDate($frm["dh_s"]),
				fStrToDate($frm["dh_r"]),
				fReturnStringNull($frm["id_cad_eventos"]),
				fReturnStringNull(trim($frm["ds"])),
				fReturnStringNull(trim($frm["ds_tema"])),
				fReturnStringNull(trim($frm["ds_org"])),
				fReturnStringNull(trim($frm["ds_dest"])),
				fReturnStringNull(trim($frm["ds_orig"])),
				fReturnStringNull(trim($frm["tp_autoriz"])),
				fReturnStringNull(trim($frm["fg_imprimir"]))
			);
			CONN::get()->execute("
				INSERT INTO EVE_SAIDA(
					DH_S,
					DH_R,
					ID_CAD_EVENTOS,
					DS,
					DS_TEMA,
					DS_ORG,
					DS_DEST,
					DS_ORIG,
					TP_AUTORIZ,
					FG_IMPRIMIR
				) VALUES (?,?,?,?,?,?,?,?,?,?)",$arr);
			$id = CONN::get()->Insert_ID();
			fSaidaMembro( $id, $particip );
			$out["id"] = $id;
		endif;
		$out["success"] = true;

	//EXCLUSAO DE SAIDA
	elseif ( $op == "DELETE" ):
		fSaidaMembro( $parameters["id"], array() );
		CONN::get()->execute("DELETE FROM EVE_SAIDA WHERE ID = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	//GET SAIDA
	else:
		$out["saida"] = array( "id" => $parameters["id"] );
		$out["membros"] = getMembros( $parameters );
		if ( $parameters["id"] <> "Novo" ):
			$result = CONN::get()->execute("SELECT * FROM EVE_SAIDA WHERE ID = ?", array( $parameters["id"] ) );
			if (!$result->EOF):
				$out["success"] = true;
				$out["saida"] = array(
						"id"				=> $result->fields['ID'],
						"dh_s"				=> strtotime($result->fields['DH_S'])."000",
						"dh_r"				=> strtotime($result->fields['DH_R'])."000",
						"id_cad_eventos"	=> $result->fields['ID_CAD_EVENTOS'],
						"ds"				=> (trim($result->fields['DS'])),
						"ds_tema"			=> (trim($result->fields['DS_TEMA'])),
						"ds_org"			=> (trim($result->fields['DS_ORG'])),
						"ds_dest"			=> (trim($result->fields['DS_DEST'])),
						"ds_orig"			=> (trim($result->fields['DS_ORIG'])),
						"tp_autoriz"		=> $result->fields['TP_AUTORIZ'],
						"fg_imprimir"		=> $result->fields['FG_IMPRIMIR']
					);
			endif;
		endif;
	endif;
	return $out;
}

function fSaidaMembro( $saidaID, $arrayParticip ) {

	$result = CONN::get()->execute("
		SELECT *
		FROM EVE_SAIDA_MEMBRO
		WHERE ID_EVE_SAIDA = ?
	", array( $saidaID ) );
	foreach ($result as $k => $f):
		$esp[] = $f;
	endforeach;

	CONN::get()->execute("DELETE FROM EVE_SAIDA_MEMBRO WHERE ID_EVE_SAIDA = ?", Array( $saidaID ) );
	if ( count($arrayParticip) > 0 ):
		CONN::get()->execute("
			INSERT INTO EVE_SAIDA_MEMBRO (ID_EVE_SAIDA, ID_CAD_MEMBRO)
			SELECT ?, ID FROM CAD_MEMBRO WHERE ID IN (". implode(',',$arrayParticip) .")
		", array($saidaID) );

		foreach ($esp as $k => $f):
			CONN::get()->execute("
				UPDATE EVE_SAIDA_MEMBRO SET
					BUS = ?,
					TENT = ?,
					KITCHEN = ?
				WHERE ID_CAD_MEMBRO = ?
				  AND ID_EVE_SAIDA = ?
			", array( $f["BUS"], $f["TENT"], $f["KITCHEN"], $f["ID_CAD_MEMBRO"], $saidaID ) );
		endforeach;

		$aAutoriz = array();
		$result = CONN::get()->execute("
			SELECT cm.ID
			FROM CAD_MEMBRO cm
			INNER JOIN CON_PESSOA cp ON (cp.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
			INNER JOIN EVE_SAIDA_MEMBRO e ON (e.ID_CAD_MEMBRO = cm.ID)
			INNER JOIN EVE_SAIDA es ON (es.ID = e.ID_EVE_SAIDA)
			WHERE e.ID_EVE_SAIDA = ?
				AND ( YEAR(es.DH_R)-YEAR(cp.DT_NASC) - IF(DATE_FORMAT(cp.DT_NASC,'%m%d')>DATE_FORMAT(es.DH_R,'%m%d'),1,0) < 18 )
		", array( $saidaID ) );
		foreach ($result as $k => $fields):
			$aAutoriz[] = $fields["ID"];
		endforeach;

		if (count($aAutoriz) > 0):
			CONN::get()->execute("
				UPDATE EVE_SAIDA_MEMBRO SET FG_AUTORIZ = 'S'
				WHERE ID_CAD_MEMBRO IN (".implode(",",$aAutoriz) .")
				  AND ID_EVE_SAIDA = ?
			", array( $saidaID ) );
		endif;
	endif;
}

function getMembros( $parameters ) {
	$arr = array();

	$qtdZeros = zeroSizeID();
	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$idade = is_null($fields['IDADE_EVENTO_FIM']) ? $fields['IDADE_HOJE'] : $fields['IDADE_EVENTO_FIM'];
		$fgAutoriz = $fields['FG_AUTORIZ'];

		if ( is_null($fgAutoriz) || is_null($fields['ID_EVE_SAIDA']) ):
			$fgAutoriz = ($idade < 18 ? 'S' : 'N');
		endif;
		$arr[] = array(
			"pt" => is_null($fields['ID_EVE_MEMBRO']) ? 'N' : 'S',
			"id" => $fields['ID_CAD_MEMBRO'],
			"nm" => $fields['NM'],
			"fg" => $fgAutoriz,
			"sb" => fStrZero($fields['ID_CAD_PESSOA'], $qtdZeros)
		);
	endforeach;
	return $arr;
}

function getMembrosFilter( $parameters ) {
	$arr = array();


	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$arr[] = $fields['ID_CAD_MEMBRO'];
	endforeach;
	return array( "membros" => getMembros( array( "dhr" => $parameters["dhr"], "id" => $parameters["id"] ) ), "filter" => $arr );
}

function getSaidas( $parameters ) {
	$arr = array();

	session_start();
	$usuarioID = $_SESSION['USER']['id'];



	$query = "SELECT es.ID, es.DS, es.DS_DEST, es.DH_S, es.DH_R FROM EVE_SAIDA es";
	if ( $parameters["filter"] == "Y" ):
		$query .= " WHERE YEAR(es.DH_S) = YEAR(NOW()) OR YEAR(es.DH_R) = YEAR(NOW())";
	elseif ( $parameters["filter"] == "P" ):
		$query .= " WHERE es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S' AND EXISTS (SELECT 1 FROM EVE_SAIDA_MEMBRO WHERE ID_EVE_SAIDA = es.ID AND FG_AUTORIZ = 'S' ";

    	//MEMBRO LOGADO
    	$result = CONN::get()->execute("
    		SELECT cu.ID_CAD_MEMBRO, ca.ID_UNIDADE, ca.CD_CARGO, ca.CD_CARGO2, ca.NM, ca.IDADE_HOJE
    		  FROM CON_ATIVOS ca
    	INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
    	     WHERE cu.ID = ?
    	", array( $usuarioID ) );
    	if ( !$result->EOF && fStrStartWith($result->fields["CD_CARGO"], "1") ):
    	    $query .= " AND ID_CAD_MEMBRO = ".$result->fields["ID_CAD_MEMBRO"];
    	endif;
		 $query .= "  )";

	endif;
	$query .= " ORDER BY es.DH_S DESC";

	$result = CONN::get()->execute($query);
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => fStrZero($fields['ID'], 3),
			"ds" => ($fields['DS']),
			"dst" => ($fields['DS_DEST']),
			"dh_s" => strtotime($fields['DH_S']),
			"dh_r" => strtotime($fields['DH_R'])
		);
	endforeach;
	return array( "result" => true, "saidas" => $arr );
}

function getAttrib( $parameters ) {
	$arr = array();
	$filter = strtoupper($parameters["filter"]);
	if (!empty($filter)):

		$result = CONN::get()->execute("
			SELECT esp.ID, ca.NM, ca.DS_UNIDADE, esp.$filter
			FROM EVE_SAIDA es
			INNER JOIN EVE_SAIDA_MEMBRO esp ON (esp.ID_EVE_SAIDA = es.ID)
	        INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO)
	        WHERE es.ID = ?
			ORDER BY ca.NM
		", array( $parameters["id"] ) );
		foreach ($result as $k => $f):
			$arr[] = array(
				"id" => $f['ID'],
				"nm" => $f["NM"],
				"un" => $f["DS_UNIDADE"],
				"cd" => fReturnStringNull($f[$filter],"")
			);
		endforeach;
	endif;
	return array( "people" => $arr );
}

function setAttrib( $parameters ) {


	$fl = $parameters["fl"];
	$vl = fReturnStringNull( $parameters["vl"] );

	CONN::get()->execute("
		UPDATE EVE_SAIDA_MEMBRO SET
			$fl = ?
		WHERE ID = ?
	", array( $vl, $parameters["id"] ) );

	return array( "result" => true );
}
?>
