<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	$where = "";
	$aWhere = array();
	
	$strQuery = "
		SELECT 
				e.ID AS ID_EVE_PESSOA, 
				es.ID AS ID_EVE_SAIDA, a.IDADE_HOJE, 
				YEAR(es.DH_R)-YEAR(a.DT_NASC) - IF(DATE_FORMAT(a.DT_NASC,'%m%d')>DATE_FORMAT(es.DH_R,'%m%d'),1,0) AS IDADE_EVENTO_FIM,
				a.ID, a.NM, e.FG_AUTORIZ 
		FROM CON_ATIVOS a 
		LEFT JOIN EVE_SAIDA_PESSOA e ON (e.ID_CAD_PESSOA = a.ID AND e.ID_EVE_SAIDA = ?) 
		LEFT JOIN EVE_SAIDA es ON (es.ID = e.ID_EVE_SAIDA)
		WHERE 1=1 
	";
	if ($parameters["id"] == "Novo"):
		$aWhere[] = null;
	else:
		$aWhere[] = $parameters["id"];
	endif;
	
	if ( isset($parameters["dhr"]) ):
		$dhr = fStrToDate($parameters["dhr"]);
		$where .= " AND YEAR(DATE('$dhr'))-YEAR(a.DT_NASC) - IF(DATE_FORMAT(a.DT_NASC,'%m%d')>DATE_FORMAT(DATE('$dhr'),'%m%d'),1,0) < ?";		
		$aWhere[] = 18;
	//else:
	//	$where .= " a.IDADE_HOJE < ?";
		//$aWhere[] = 18;
	endif;
	
	if ( isset($parameters["filters"]) ):
		$keyAnt = "";
		foreach ($parameters["filters"] as $key => $v):
			$not = false;
			if ( isset($parameters["filters"][$key]["fg"]) ):
				$not = strtolower($parameters["filters"][$key]["fg"]) == "true";
			endif;
			$notStr = ( $not ? "NOT " : "" );
			if ( $key == "X" ):
				$where .= " AND a.tp_sexo ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND a.id_unidade ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND EXISTS (
							SELECT DISTINCT 1
							FROM TAB_APRENDIZADO ta 
							LEFT JOIN APR_HISTORICO ah ON (ah.id_tab_aprend = ta.id AND ah.dt_conclusao IS NULL)
							WHERE ta.tp_item = ? AND ah.id_cad_pessoa = a.ID AND
							ta.ID ".$notStr."IN";
				$aWhere[] = "CL";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $parameters["filters"][$key]["vl"] ) ):
				foreach ($parameters["filters"][$key]["vl"] as $value):
					if ( $key == "B" ):
						if ($value == "S"):
							$where .= (!$prim ? " OR " : "") ."a.dt_bat IS ". ( $value == "S" && !$not ? "NOT NULL" : "NULL");
						elseif ($value == "N"):
							$where .= (!$prim ? " OR " : "") ."a.dt_bat IS ". ( $value == "N" && !$not ? "NULL" : "NOT NULL");
						elseif (fStrStartWith($value,"A")):
							$where .= (!$prim ? " OR " : "") ."YEAR(a.dt_bat) ". ( !$not ? " < " : " >= ") . substr($value,1,4);
						else:
							$where .= (!$prim ? " OR " : "") ."YEAR(a.dt_bat) ". ( !$not ? " = " : " <> ") . $value;
						endif;
					elseif ( $key == "G" ):
						if ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."a.CD_FANFARRA IS ".( !$not ? "NOT NULL" : "NULL");
						elseif ( $value == "4" ):
						    $where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-04%'");
						elseif ( $value == "5" ):
						    $where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-07%'");
						elseif ( $value == "6" ):
						    $where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '1-01%'");
						else:
							$where .= (!$prim ? " OR " : "") ."a.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
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
	$strQuery .= " $where ORDER BY a.NM";
	
//echo "$strQuery\n";
//print_r($aWhere);
//exit;

	return $GLOBALS['conn']->Execute($strQuery, $aWhere);
}

function getNames(){
	$arr = array();

	session_start();
	$usuarioID = $_SESSION['USER']['id_usuario'];
	
	$unidadeID	= null;
	$membroID	= null;
	$cargo		= null;
	
	fConnDB();

	//MEMBRO LOGADO
	$result = $GLOBALS['conn']->Execute("
		SELECT cu.ID_CAD_PESSOA, ca.ID_UNIDADE, ca.CD_CARGO, ca.CD_CARGO2, ca.NM, ca.IDADE_HOJE
		  FROM CON_ATIVOS ca
	INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID)
	     WHERE cu.ID_USUARIO = ? 
	", array( $usuarioID ) );
	if (!$result->EOF):
		$unidadeID = $result->fields["ID_UNIDADE"];
		$membroID = $result->fields["ID_CAD_PESSOA"];
		$membroNM = utf8_encode($result->fields["NM"]);
		
		$rs = $GLOBALS['conn']->Execute("
			SELECT 1 
			FROM EVE_SAIDA_PESSOA esp
	  INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = ?)
		   WHERE esp.ID_CAD_PESSOA = ?
		     AND esp.FG_AUTORIZ = ?
		", array( "S", $membroID, "S" ) );
		if ($result->fields["IDADE_HOJE"] < 18 && !$rs->EOF):
			$arr[] = array( "id" => $membroID, "ds" => "<<mim>> - $membroNM");
		endif;
		
		$cargo = $result->fields['CD_CARGO'];
		if (fStrStartWith($cargo,"2-07")):
			$cargo = $result->fields['CD_CARGO2'];
		endif;
	endif;
	
	$aQuery = array( "query" => "", "binds" => array() );
	
	//TRATAMENTO MEMBROS DA MINHA UNIDADE
	$aQuery = getUnionByUnidade( $aQuery, $unidadeID, $membroID );
		
	//TRATAMENTO MEMBROS QUE ESTAO FAZENDO AS MESMAS CLASSES QUE EU
	$aQuery = getUnionByClasses( $aQuery, $membroID );

	//TRATAMENTO PARA INSTRUTOR DE CLASSE
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$classe = "01-".substr($cargo,-2);

		$aQuery["query"] .= " UNION 
			SELECT DISTINCT ca.NM, ca.ID
			  FROM CON_APR_PESSOA cap
		    INNER JOIN CON_ATIVOS ca ON (ca.ID = cap.ID_CAD_PESSOA)
	            INNER JOIN EVE_SAIDA_PESSOA esp ON (esp.ID_CAD_PESSOA = ca.ID AND esp.FG_AUTORIZ = ?)
	            INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = ?)
			 WHERE cap.CD_ITEM_INTERNO LIKE '$classe%'
			   AND cap.DT_CONCLUSAO IS NULL
			   AND ca.ID <> ?
			   AND ca.IDADE_HOJE < ?";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = $membroID;
		$aQuery["binds"][] = 18;
	endif;
	
	//TRATAMENTO PARA ADMINISTRACAO/INSTRUTORES NAO ESPECIFICOS
	if ($cargo == "2-04-00" || $cargo == "2-04-99" || fStrStartWith($cargo,"2-01") || fStrStartWith($cargo,"2-02")):
		$aQuery["query"] .= " UNION 
	           SELECT ca.NM, ca.ID 
	           FROM CON_ATIVOS ca
	     INNER JOIN EVE_SAIDA_PESSOA esp ON (esp.ID_CAD_PESSOA = ca.ID AND esp.FG_AUTORIZ = ?)
	     INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = ?)
	           WHERE ca.ID <> ? 
	             AND ca.IDADE_HOJE < ?";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = $membroID;
		$aQuery["binds"][] = 18;
	endif;
	
	//TRATAMENTO MEUS DEPENDENTES, SUAS UNIDADES OU SUAS CLASSES
	$rd = $GLOBALS['conn']->Execute("
		SELECT ca.ID, ca.NM, ca.ID_UNIDADE
		FROM CAD_USUARIOS cu
		INNER JOIN CAD_RESP cr ON (REPLACE(REPLACE(cr.CPF_RESP,'.',''),'-','') = cu.CD_USUARIO)
		INNER JOIN CON_ATIVOS ca ON (ca.ID_RESP = cr.ID)
	        INNER JOIN EVE_SAIDA_PESSOA esp ON (esp.ID_CAD_PESSOA = ca.ID AND esp.FG_AUTORIZ = ?)
	        INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = ?)
		WHERE cu.ID_USUARIO = ?
	          AND ca.IDADE_HOJE < ?
	", array("S", "S", $usuarioID, 18) );
	foreach ($rd as $k => $l):
		$arr[] = array( "id" => $l["ID"], "ds" =>utf8_encode($l["NM"]) );
		
		$aQuery = getUnionByUnidade( $aQuery, $l["ID_UNIDADE"], $l["ID"] );
		$aQuery = getUnionByClasses( $aQuery, $l["ID"] );
	endforeach;
	
	if (!empty($aQuery["query"])):
		$rs = $GLOBALS['conn']->Execute( substr($aQuery["query"], 7)." ORDER BY 1", $aQuery["binds"] );
		if (!$rs->EOF):
			foreach ($rs as $k => $line):
				$arr[] = array( "id" => $line["ID"], "ds" => utf8_encode($line["NM"]) );
			endforeach;
		endif;
	endif;
	
	return array( "result" => true, "names" => $arr );
}

function getUnionByUnidade($aQuery, $unidadeID, $membroID){
	if (!is_null($unidadeID) && !is_null($membroID)):
		$aQuery["query"] .=" UNION 
		SELECT ca.NM, ca.ID 
		FROM CON_ATIVOS ca 
      INNER JOIN EVE_SAIDA_PESSOA esp ON (esp.ID_CAD_PESSOA = ca.ID AND esp.FG_AUTORIZ = ?)
	  INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = ?)
		WHERE ca.ID_UNIDADE = ? 
		  AND ca.ID <> ? 
		  AND ca.IDADE_HOJE < ?";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = $unidadeID;
		$aQuery["binds"][] = $membroID;
		$aQuery["binds"][] = 18;
	endif;
	return $aQuery;
}

function getUnionByClasses($aQuery, $membroID){
	if (!is_null($membroID)):
		$aQuery["query"] .= " UNION 
		SELECT DISTINCT ca.NM, ca.ID
		  FROM CON_APR_PESSOA cap
	INNER JOIN CON_ATIVOS ca ON (ca.ID = cap.ID_CAD_PESSOA)
    INNER JOIN EVE_SAIDA_PESSOA esp ON (esp.ID_CAD_PESSOA = ca.ID AND esp.FG_AUTORIZ = ?)
	INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA AND es.DH_R > NOW() AND es.FG_IMPRIMIR = ?)
		 WHERE cap.CD_ITEM_INTERNO IN (SELECT DISTINCT CD_ITEM_INTERNO FROM CON_APR_PESSOA WHERE ID_CAD_PESSOA = ? AND TP_ITEM = ? AND DT_CONCLUSAO IS NULL)
		   AND cap.DT_CONCLUSAO IS NULL
		   AND ca.ID <> ?
		   AND ca.IDADE_HOJE < ?";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = "S";
		$aQuery["binds"][] = $membroID;
		$aQuery["binds"][] = "CL";
		$aQuery["binds"][] = $membroID;
		$aQuery["binds"][] = 18;
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

	fConnDB();

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
				fReturnStringNull(trim($frm["fg_campori"])),
				fReturnStringNull(trim($frm["fg_imprimir"])),
				$frm["id"]
			);
			$GLOBALS['conn']->Execute("
				UPDATE EVE_SAIDA SET
					DH_S = ?,
					DH_R = ?,
					ID_CAD_EVENTOS = ?,
					DS = ?,
					DS_TEMA = ?,
					DS_ORG = ?,
					DS_DEST = ?,
					DS_ORIG = ?,
					FG_CAMPORI = ?,
					FG_IMPRIMIR = ?
				WHERE ID = ?",$arr);
			
			fSaidaPessoa( $frm["id"], $particip );
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
				fReturnStringNull(trim($frm["fg_campori"])),
				fReturnStringNull(trim($frm["fg_imprimir"]))
			);
			$GLOBALS['conn']->Execute("
				INSERT INTO EVE_SAIDA(
					DH_S,
					DH_R,
					ID_CAD_EVENTOS,
					DS,
					DS_TEMA,
					DS_ORG,
					DS_DEST,
					DS_ORIG,
					FG_CAMPORI,
					FG_IMPRIMIR
				) VALUES (?,?,?,?,?,?,?,?,?,?)",$arr);
			$id = $GLOBALS['conn']->Insert_ID();
			fSaidaPessoa( $id, $particip );
			$out["id"] = $id;
		endif;
		$out["success"] = true;

	//EXCLUSAO DE SAIDA
	elseif ( $op == "DELETE" ):
		fSaidaPessoa( $parameters["id"], array() );
		$GLOBALS['conn']->Execute("DELETE FROM EVE_SAIDA WHERE ID = ?", Array( $parameters["id"] ) );
		$out["success"] = true;

	//GET SAIDA
	else:
		$out["saida"] = array( "id" => $parameters["id"] );
		$out["membros"] = getMembros( $parameters );
		if ( $parameters["id"] <> "Novo" ):
			$result = $GLOBALS['conn']->Execute("SELECT * FROM EVE_SAIDA WHERE ID = ?", array( $parameters["id"] ) );
			if (!$result->EOF):
				$out["success"] = true;
				$out["saida"] = array(
						"id"				=> $result->fields['ID'],
						"dh_s"				=> strtotime($result->fields['DH_S'])."000",
						"dh_r"				=> strtotime($result->fields['DH_R'])."000",
						"id_cad_eventos"	=> $result->fields['ID_CAD_EVENTOS'],
						"ds"				=> utf8_encode(trim($result->fields['DS'])),
						"ds_tema"			=> utf8_encode(trim($result->fields['DS_TEMA'])),
						"ds_org"			=> utf8_encode(trim($result->fields['DS_ORG'])),
						"ds_dest"			=> utf8_encode(trim($result->fields['DS_DEST'])),
						"ds_orig"			=> utf8_encode(trim($result->fields['DS_ORIG'])),
						"fg_campori"		=> $result->fields['FG_CAMPORI'],
						"fg_imprimir"		=> $result->fields['FG_IMPRIMIR']
					);
			endif;
		endif;
	endif;
	return $out;
}

function fSaidaPessoa( $saidaID, $arrayParticip ) {
	
	$result = $GLOBALS['conn']->Execute("
		SELECT ID_CAD_PESSOA, BUS, TENT, KITCHEN
		FROM EVE_SAIDA_PESSOA 
		WHERE ID_EVE_SAIDA = ?
	", array( $saidaID ) );	
	foreach ($result as $k => $f):
		$esp[] = $f;
	endforeach;

	$GLOBALS['conn']->Execute("DELETE FROM EVE_SAIDA_PESSOA WHERE ID_EVE_SAIDA = ?", Array( $saidaID ) );
	if ( count($arrayParticip) > 0 ):
		$GLOBALS['conn']->Execute("
			INSERT INTO EVE_SAIDA_PESSOA (ID_EVE_SAIDA, ID_CAD_PESSOA)
			SELECT ?, ID FROM CON_ATIVOS WHERE ID IN (". implode(',',$arrayParticip) .") ORDER BY NM
		", array($saidaID) );
		
		foreach ($esp as $k => $f):
			$GLOBALS['conn']->Execute("
				UPDATE EVE_SAIDA_PESSOA SET 
					BUS = ?, 
					TENT = ?, 
					KITCHEN = ?
				WHERE ID_CAD_PESSOA = ?
				  AND ID_EVE_SAIDA = ?
			", array( $f["BUS"], $f["TENT"], $f["KITCHEN"], $f["ID_CAD_PESSOA"], $saidaID ) );
		endforeach;

		$aAutoriz = array();
		$result = $GLOBALS['conn']->Execute("
			SELECT a.ID
			FROM CON_ATIVOS a
			INNER JOIN EVE_SAIDA_PESSOA e ON (e.ID_CAD_PESSOA = a.ID)
			INNER JOIN EVE_SAIDA es ON (es.ID = e.ID_EVE_SAIDA)
			WHERE e.ID_EVE_SAIDA = ? 
				AND ( YEAR(es.DH_R)-YEAR(a.DT_NASC) - IF(DATE_FORMAT(a.DT_NASC,'%m%d')>DATE_FORMAT(es.DH_R,'%m%d'),1,0) < ? )
		", array( $saidaID, 18 ) );
		foreach ($result as $k => $fields):
			$aAutoriz[] = $fields["ID"];
		endforeach;
		
		if (count($aAutoriz) > 0):
			$GLOBALS['conn']->Execute("
				UPDATE EVE_SAIDA_PESSOA SET FG_AUTORIZ = 'S'
				WHERE ID_CAD_PESSOA IN (".implode(",",$aAutoriz) .")
				  AND ID_EVE_SAIDA = ?
			", array( $saidaID ) );
		endif;
	endif;
}

function getMembros( $parameters ) {
	$arr = array();
	fConnDB();
	
	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$idade = is_null($fields['IDADE_EVENTO_FIM']) ? $fields['IDADE_HOJE'] : $fields['IDADE_EVENTO_FIM'];
		$fgAutoriz = $fields['FG_AUTORIZ'];
		
		if ( is_null($fgAutoriz) || is_null($fields['ID_EVE_SAIDA']) ):
			$fgAutoriz = ($idade < 18 ? 'S' : 'N');
		endif;
	
		$arr[] = array(
			"pt" => is_null($fields['ID_EVE_PESSOA']) ? 'N' : 'S',
			"id" => $fields['ID'],
			"nm" => utf8_encode($fields['NM']),
			"fg" => $fgAutoriz
		);
	endforeach;
	return $arr;
}

function getMembrosFilter( $parameters ) {
	$arr = array();
	fConnDB();

	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$arr[] = $fields['ID'];
	endforeach;
	return array( "membros" => getMembros( array( "dhr" => $parameters["dhr"], "id" => $parameters["id"] ) ), "filter" => $arr );
}

function getSaidas( $parameters ) {
	$arr = array();
	
	session_start();
	$usuarioID = $_SESSION['USER']['id_usuario'];	
	
	fConnDB();
	
	$query = "SELECT es.ID, es.DS, es.DS_DEST, es.DH_S, es.DH_R FROM EVE_SAIDA es";
	if ( $parameters["filter"] == "Y" ):
		$query .= " WHERE YEAR(es.DH_R) = YEAR(NOW())";
	elseif ( $parameters["filter"] == "P" ):
		$query .= " WHERE es.DH_R > NOW() AND es.FG_IMPRIMIR = 'S' AND EXISTS (SELECT 1 FROM EVE_SAIDA_PESSOA WHERE ID_EVE_SAIDA = es.ID AND FG_AUTORIZ = 'S' ";
    
    	//MEMBRO LOGADO
    	$result = $GLOBALS['conn']->Execute("
    		SELECT cu.ID_CAD_PESSOA, ca.ID_UNIDADE, ca.CD_CARGO, ca.CD_CARGO2, ca.NM, ca.IDADE_HOJE
    		  FROM CON_ATIVOS ca
    	INNER JOIN CAD_USUARIOS cu ON (cu.ID_CAD_PESSOA = ca.ID)
    	     WHERE cu.ID_USUARIO = ? 
    	", array( $usuarioID ) );
    	if ( !$result->EOF && fStrStartWith($result->fields["CD_CARGO"], "1") ):
    	    $query .= " AND ID_CAD_PESSOA = ".$result->fields["ID_CAD_PESSOA"];
    	endif;
		 $query .= "  )";
		 
	endif;
	$query .= " ORDER BY es.DH_S DESC";
	
	$result = $GLOBALS['conn']->Execute($query);
	foreach ($result as $k => $fields):
		$arr[] = array( 
			"id" => str_pad($fields['ID'], 3, "0", STR_PAD_LEFT),
			"ds" => utf8_encode($fields['DS']),
			"dst" => utf8_encode($fields['DS_DEST']),
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
		fConnDB();
		$result = $GLOBALS['conn']->Execute("
			SELECT esp.ID, ca.NM, ca.DS_UNIDADE, esp.$filter
			FROM EVE_SAIDA es
			INNER JOIN EVE_SAIDA_PESSOA esp ON (esp.ID_EVE_SAIDA = es.ID)
	        INNER JOIN CON_ATIVOS ca ON (ca.ID = esp.ID_CAD_PESSOA)
	        WHERE es.ID = ?
			ORDER BY ca.NM
		", array( $parameters["id"] ) );
		foreach ($result as $k => $f):
			$arr[] = array(
				"id" => $f['ID'],
				"nm" => utf8_encode($f["NM"]),
				"un" => utf8_encode($f["DS_UNIDADE"]),
				"cd" => utf8_encode($f[$filter])
			);
		endforeach;
	endif;
	return array( "people" => $arr );
}

function setAttrib( $parameters ) {
	fConnDB();
	
	$fl = $parameters["fl"];
	$vl = fReturnStringNull( $parameters["vl"] );
	
	$GLOBALS['conn']->Execute("
		UPDATE EVE_SAIDA_PESSOA SET
			$fl = ?
		WHERE ID = ?
	", array( $vl, $parameters["id"] ) );

	return array( "result" => true );
}
?>