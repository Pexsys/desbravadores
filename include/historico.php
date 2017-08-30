<?php
@require_once("compras.php");

function dateDefaultInicio($date = null){
	return ( is_null($date) ? date( "Y-m-d", mktime( 0, 0, 0, 0, 1, date("Y") ) ) : $date );
}

function getParamDates( $frm ) {
	return array(
		"dt_inicio"			=> $frm["fg_inicio_alt"] == "S"			? dateDefaultInicio(getDateNull($frm["dt_inicio"])) : "N",
		"dt_conclusao"		=> $frm["fg_conclusao_alt"] == "S"		? getDateNull($frm["dt_conclusao"]) 				: "N",
		"dt_avaliacao"		=> $frm["fg_avaliacao_alt"] == "S"		? getDateNull($frm["dt_avaliacao"]) 				: "N",
		"dt_investidura"	=> $frm["fg_investidura_alt"] == "S"	? getDateNull($frm["dt_investidura"]) 				: "N"
	);
}

function updateHistoricoQuery( $paramDates, $sWhere ){
	$strUpdate = "UPDATE APR_HISTORICO SET";
	$hasField = false;
	$aBind = array();
	
	if ($paramDates["dt_inicio"] != "N"):
		$strUpdate .= ($hasField ? "," : "") . " DT_INICIO = ?";
		$aBind[] = $paramDates["dt_inicio"];
		$hasField = true;
	endif;
	if ($paramDates["dt_conclusao"] != "N"):
		$strUpdate .= ($hasField ? "," : "") . " DT_CONCLUSAO = ?";
		$aBind[] = $paramDates["dt_conclusao"];
		$hasField = true;
	endif;
	if ($paramDates["dt_avaliacao"] != "N"):
		$strUpdate .= ($hasField ? "," : "") . " DT_AVALIACAO = ?";
		$aBind[] = $paramDates["dt_avaliacao"];
		$hasField = true;
	endif;
	if ($paramDates["dt_investidura"] != "N"):
		$strUpdate .= ($hasField ? "," : "") . " DT_INVESTIDURA = ?";
		$aBind[] = $paramDates["dt_investidura"];
		$hasField = true;
	endif;
	$strUpdate .= " WHERE $sWhere";
	return array( "query" => $strUpdate, "bind" => $aBind );
}

function updateHistorico( $barpessoaid, $barfnid, $paramDates, $compras = null ) {
	if (is_null($compras)):
		$compras = new COMPRAS();
	endif;
	
	$str = "";
	$id = null;
	$arrayDB = array();
	
	//PESQUISAR SE EXISTE O ITEM
	$rh = $GLOBALS['conn']->Execute("
		SELECT ah.ID, ah.DT_INICIO, ah.DT_CONCLUSAO, ah.DT_AVALIACAO, ah.DT_INVESTIDURA
		  FROM APR_HISTORICO	ah
	INNER JOIN CON_ATIVOS 		at ON (at.ID = ah.ID_CAD_PESSOA)
		 WHERE ah.ID_CAD_PESSOA = ?
		   AND ah.ID_TAB_APREND = ?
	", array( $barpessoaid, $barfnid ) );
	
	//SE NAO EXISTE, INSERE
	if ($rh->EOF):
		$arrayDB = array( $barpessoaid, $barfnid, ($paramDates["dt_inicio"] == "N" ? dateDefaultInicio() : $paramDates["dt_inicio"]) );
		
		$queryInsert1 = "INSERT INTO APR_HISTORICO(
			ID_CAD_PESSOA, 
			ID_TAB_APREND, 
			DT_INICIO";

		$queryInsert2 = "VALUES (?,?,?";
		
		if ($paramDates["dt_conclusao"] != "N"):
			$queryInsert1 .= ",DT_CONCLUSAO";
			$queryInsert2 .= ",?";
			$arrayDB[] = $paramDates["dt_conclusao"];
		endif;
		if ($paramDates["dt_avaliacao"] != "N"):
			$queryInsert1 .= ",DT_AVALIACAO";
			$queryInsert2 .= ",?";
			$arrayDB[] = $paramDates["dt_avaliacao"];
		endif;
		if ($paramDates["dt_investidura"] != "N"):
			$queryInsert1 .= ",DT_INVESTIDURA";
			$queryInsert2 .= ",?";
			$arrayDB[] = $paramDates["dt_investidura"];
		endif;
		
		$GLOBALS['conn']->Execute("$queryInsert1) $queryInsert2)", $arrayDB );
		$id = $GLOBALS['conn']->Insert_ID();
		$str = "INSERT";

	//SE EXISTE, ATUALIZA
	else:
		$id = $rh->fields["ID"];
	
		$queryUpdate = "UPDATE APR_HISTORICO SET";
		$arrayDB = array();
		
		$bUp = false;
		if ($paramDates["dt_inicio"] != "N"):
			$queryUpdate .= " DT_INICIO = ?";
			$arrayDB[] = is_null($paramDates["dt_inicio"]) ? dateDefaultInicio() : $paramDates["dt_inicio"];
			$bUp = true;
		endif;
		if ($paramDates["dt_conclusao"] != "N"):
			$queryUpdate .= ($bUp ? "," : "") ." DT_CONCLUSAO = ?";
			$arrayDB[] = $paramDates["dt_conclusao"];
			$bUp = true;
		endif;
		if ($paramDates["dt_avaliacao"] != "N"):
			$queryUpdate .= ($bUp ? "," : "") ." DT_AVALIACAO = ?";
			$arrayDB[] = $paramDates["dt_avaliacao"];
			$bUp = true;
		endif;
		if ($paramDates["dt_investidura"] != "N"):
			$queryUpdate .= ($bUp ? "," : "") ." DT_INVESTIDURA = ?";
			$arrayDB[] = $paramDates["dt_investidura"];
		endif;

		//SE EXISTE DATAS PARA UPDATE
		if ( count($arrayDB) > 0 ):
			$arrayDB[] = $id;
			$GLOBALS['conn']->Execute($queryUpdate. " WHERE ID = ?", $arrayDB);
			$str = "UPDATE";
			
			if ( !is_null($paramDates["dt_conclusao"]) && $paramDates["dt_conclusao"] != "N"):
				$GLOBALS['conn']->Execute("
					DELETE FROM 
					  FROM APR_PESSOA_REQ
					 WHERE ID_HISTORICO = ?
				", array($id) );
			endif;
		endif;
		
	endif;

	if (!is_null($paramDates["dt_investidura"]) && $paramDates["dt_investidura"] != "N"):
		$compras->deleteItemPessoaEntregue( $idPessoa, $id );
	endif;
	
	$consulta = consultaAprendizadoPessoa( $barfnid, $barpessoaid );
	return array( 
		"op" => $str,
		"id" => $id,
		"ar" => $consulta["ar"],
		"cd" => $consulta["cd"],
		"ap" => $consulta["ap"],
		"nm" => $consulta["nm"] );
}

function podeAtualizarDtInvestidura($pessoaID,$aprendID){
	$result = $GLOBALS['conn']->Execute("
		SELECT 1
		  FROM CON_COMPRAS
		WHERE FG_ENTREGUE = 'N'
		  AND ID_CAD_PESSOA = ?
		  AND ID_TAB_APREND = ?
	", array($pessoaID,$aprendID) );
	if ($result->EOF):
		return true;
	endif;
	return false;
}

function analiseHistoricoPessoa($pessoaID){
	$arr = array();
	$alterados = 0;
	$deletados = 0;
	
	$res = $GLOBALS['conn']->Execute("
		  SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.DT_INICIO, 
			 MAX(DT_ASSINATURA) AS DT_CONCLUSAO, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		   WHERE ta.ID_CAD_PESSOA = ?
			 AND ta.DT_CONCLUSAO IS NULL
		GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.DT_INICIO
		ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
	", array( $pessoaID ) );

	foreach ($res as $kes => $les):
		$rc = $GLOBALS['conn']->Execute("
			SELECT COUNT(*) AS QT_COMPL
			  FROM CON_APR_PESSOA
			 WHERE ID_CAD_PESSOA = ?
			   AND ID_TAB_APREND = ?
			   AND DT_ASSINATURA IS NOT NULL
			   AND DT_CONCLUSAO IS NULL
		", array( $pessoaID, $les["ID_TAB_APREND"] ) );

		if (!$rc->EOF):
			$qtdCompl = $rc->fields["QT_COMPL"];
			$reqok = ($qtdCompl >= $les["QT_REQ"]);

			$pq = $GLOBALS['conn']->Execute("
				SELECT h.ID
				  FROM APR_HISTORICO h
				INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = h.ID_TAB_APREND)
				 WHERE h.ID_CAD_PESSOA = ?
				   AND ta.TP_ITEM = ?
				   AND ta.CD_ITEM_INTERNO = ?
				   AND h.DT_CONCLUSAO IS NULL
			", array( $pessoaID, $les['TP_ITEM'], $les['CD_ITEM_INTERNO'] ) );

			$op = "";
			if ($reqok == true && !$pq->EOF):
				$GLOBALS['conn']->Execute("
					UPDATE APR_HISTORICO 
					   SET DT_CONCLUSAO = ? 
					 WHERE ID = ?
				", array( $les['DT_CONCLUSAO'], $pq->fields['ID'] ) );
				
				$op .= ",UPDATED";
				$alterados++;
			else:
				$op .= ",NOT UPDATED";
			endif;
			if ( $reqok == true ):
				$GLOBALS['conn']->Execute("
					DELETE FROM APR_PESSOA_REQ 
					WHERE ID_APR_PESSOA IN (SELECT ID FROM APR_PESSOA WHERE ID_CAD_PESSOA = ? AND ID_TAB_APREND = ?)	
				", array( $pessoaID, $les["ID_TAB_APREND"] ) );
				
				$op .= ",DELETED";
				$deletados++;
			endif;
			$arr[] = "HISTORICO APR[".$les["ID_TAB_APREND"]."],PESSOA[$pessoaID],TOT[".$les["QT_REQ"]."],DONE[$qtdCompl]$op";
		endif;
	endforeach;
	return array("del" => $deletados, "upd" => $alterados, "op" => $arr);
}
?>