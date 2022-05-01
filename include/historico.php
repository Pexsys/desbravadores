<?php
@require_once("_message.php");
@require_once("compras.php");

function dateDefaultInicio($date = null){
	return getDateDefault($date, date( "Y-m-d", mktime( 0, 0, 0, 0, 1, date("Y") ) ) );
}

function getParamDates( $frm ) {
	return array(
		"dt_inicio"			=> $frm["fg_inicio_alt"] == "S"			? dateDefaultInicio(getDateNull($frm["dt_inicio"])) : "N",
		"dt_conclusao"		=> $frm["fg_conclusao_alt"] == "S"		? getDateNull($frm["dt_conclusao"]) 				: "N",
		"dt_avaliacao"		=> $frm["fg_avaliacao_alt"] == "S"		? getDateNull($frm["dt_avaliacao"]) 				: "N",
		"dt_investidura"	=> $frm["fg_investidura_alt"] == "S"	? getDateNull($frm["dt_investidura"]) 				: "N"
	);
}

function marcaRequisitoID( $assDT, $histID, $reqID ) {
	
	//APAGA CASO DATA DE ASSINATURA NULA
	if ( is_null($assDT) || empty($assDT) ):
		CONN::get()->execute("
			DELETE FROM APR_PESSOA_REQ
			WHERE ID_HISTORICO = ?
				AND ID_TAB_APR_ITEM = ?
		", array( $histID, $reqID ) );

	else:
		//RECUPERA REQUISITO COM ITEM APRENDIZADO
		$rs = CONN::get()->execute("
			SELECT 1
			FROM APR_PESSOA_REQ
			WHERE ID_HISTORICO = ?
				AND ID_TAB_APR_ITEM = ?
		", array( $histID, $reqID ) );
		
		//SE NAO EXISTIR, INSERE
		if ($rs->EOF):
			CONN::get()->execute("
				INSERT INTO APR_PESSOA_REQ(
					ID_HISTORICO,
					ID_TAB_APR_ITEM,
					DT_ASSINATURA
				) VALUES (
					?,?,?
				)
			", array( $histID, $reqID, $assDT ) );
			
		//ATUALIZA
		else:
			CONN::get()->execute("
				UPDATE APR_PESSOA_REQ
					SET DT_ASSINATURA = ?
					WHERE ID_HISTORICO = ?
					AND ID_TAB_APR_ITEM ?
					AND DT_ASSINATURA <> ?
			", array( $assDT, $histID, $reqID, $assDT ) );
		endif;
	endif;
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
	
	//PESQUISA INFORMACOES SOBRE O ITEM
	$rh = CONN::get()->execute("
		SELECT TP_ITEM, CD_ITEM_INTERNO
		  FROM TAB_APRENDIZADO
		 WHERE ID = ?
	", array( $barfnid ) );
	$tpItem = $rh->fields["TP_ITEM"];
	$cdInte = $rh->fields["CD_ITEM_INTERNO"];
	
	//PESQUISAR SE EXISTE O ITEM
	$rh = CONN::get()->execute("
		SELECT ah.ID, ah.DT_INICIO, ah.DT_CONCLUSAO, ah.DT_AVALIACAO, ah.DT_INVESTIDURA
		  FROM APR_HISTORICO	ah
	INNER JOIN CON_ATIVOS 		at ON (at.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
		 WHERE ah.ID_CAD_PESSOA = ?
		   AND ah.ID_TAB_APREND = ?
	", array( $barpessoaid, $barfnid ) );
	
	//SE NAO EXISTE, INSERE
	if ($rh->EOF):
		$arrayDB = array( $barpessoaid, $barfnid, ($paramDates["dt_inicio"] == "N" ? dateDefaultInicio() : $paramDates["dt_inicio"]) );
		
		$queryInsert1 = "";
		$queryInsert2 = "";
		
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
		
		CONN::get()->execute("
    		INSERT INTO APR_HISTORICO(
    			ID_CAD_PESSOA, 
    			ID_TAB_APREND, 
    			DT_INICIO
    			$queryInsert1)
    		VALUES (?,?,?
    		    $queryInsert2)
        ", $arrayDB );
		$id = CONN::get()->Insert_ID();
		$str = "INSERT";

	//SE EXISTE, ATUALIZA
	else:
		$id = $rh->fields["ID"];
	
		$queryUpdate = "";
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
			CONN::get()->execute("UPDATE APR_HISTORICO SET $queryUpdate WHERE ID = ?", $arrayDB);
			$str = "UPDATE";
			
			if ( !is_null($paramDates["dt_conclusao"]) && $paramDates["dt_conclusao"] != "N"):
				CONN::get()->execute("
					DELETE FROM 
					  FROM APR_PESSOA_REQ
					 WHERE ID_HISTORICO = ?
				", array($id) );
			endif;
		endif;
		
	endif;

	if (!is_null($paramDates["dt_investidura"]) && $paramDates["dt_investidura"] != "N"):
		//$compras->deleteItemPessoaEntregue( $barpessoaid, $barfnid );
	endif;
	
	//VERIFICA SE ITEM É UMA ESPECIALIDADE E SE EXISTE ALGUM MESTRADO COMPLETADO COM A CONCLUSAO DESSA ESPECIALIDADE.
	if ( $tpItem == "ES" && $cdInte != "ME" && $paramDates["dt_conclusao"] != "N" ):
		regraRequisitoEspecialidade($barfnid, $barpessoaid, $paramDates["dt_inicio"], $paramDates["dt_conclusao"] );
	endif;

	//VERIFICA SE EXISTEM EXPECIALIDADES JA COMPLETADAS VALIDAS NO INICIO DE UMA CLASSE
	if ( $tpItem == "CL" ):
		$rs = CONN::get()->execute("
			SELECT DISTINCT car.ID_RQ, ah.DT_INICIO, ar.DT_CONCLUSAO
			FROM CON_APR_REQ car
			INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID AND ah.ID_CAD_PESSOA = ?)
			INNER JOIN APR_HISTORICO ar ON (ar.ID_TAB_APREND = car.ID_RQ AND ar.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
			WHERE car.ID = ?
			ORDER BY car.CD_ITEM_INTERNO
		", array($barpessoaid, $barfnid) );
		foreach($rs as $ks => $fS):
			regraRequisitoEspecialidade($fS["ID_RQ"], $barpessoaid, $fS["DT_INICIO"], $fS["DT_CONCLUSAO"]);
		endforeach;
    endif;
	
	$consulta = consultaAprendizadoPessoa( $barfnid, $barpessoaid );
	return array( 
		"op" => $str,
		"id" => $id,
		"ar" => $consulta["ar"],
		"cd" => $consulta["cd"],
		"ap" => $consulta["ap"],
		"nm" => $consulta["nm"],
		"pg" => $consulta["pg"]
	);
}

function regraRequisitoEspecialidade($barfnid, $barpessoaid, $dtInicio, $dtConclusao){
	$rg = CONN::get()->execute("
		SELECT ID, ID_TAB_APR_ITEM, TP_ITEM, MIN_AREA, DS_ITEM, FG_OBR
		  FROM CON_APR_REQ 
		 WHERE ID_RQ = ? 
	  ORDER BY CD_ITEM_INTERNO
	", array($barfnid) );
	foreach ($rg as $lg => $fg):

		//NOT EXISTS (SELECT 1 FROM CON_APR_REQ WHERE ID_RQ = car.ID_RQ AND ID = car.ID AND ID_TAB_APR_ITEM != car.ID_TAB_APR_ITEM)
		/*
		SELECT tais.ID, tais.ID_REF, tais.ID_TAB_APR_ITEM, tai.DS, tai.ID_TAB_APREND, tai.CD_REQ_INTERNO, ta.DS_ITEM
		FROM TAB_APR_ITEM_SEL tais
		INNER JOIN TAB_APR_ITEM tai ON (tai.ID = tais.ID_TAB_APR_ITEM AND tai.QT_MIN IS NOT NULL)
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = tais.ID_REF)
		WHERE EXISTS (SELECT 1 FROM CON_APR_REQ WHERE ID_RQ = tais.ID_REF AND ID = tai.ID_TAB_APREND AND ID_TAB_APR_ITEM != tais.ID_TAB_APR_ITEM)
		*/
		
		$feitas = 0;
		//LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
		$rR = CONN::get()->execute("
			SELECT tar.ID, tar.QT_MIN, COUNT(*) AS QT_FEITAS
			  FROM TAB_APR_ITEM tar
		INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_ITEM = tar.ID AND car.TP_ITEM_RQ = 'ES')
		INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
			 WHERE tar.ID = ?
		  GROUP BY tar.ID, tar.QT_MIN
		", array( $barpessoaid, $fg["ID_TAB_APR_ITEM"]  ) );
		foreach($rR as $lR => $fR):
			$feitas += min( $fR["QT_MIN"], $fR["QT_FEITAS"] );
		endforeach;
		
		$rI = CONN::get()->execute("
			SELECT ID, DT_INICIO, DT_CONCLUSAO
			FROM APR_HISTORICO
			WHERE ID_CAD_PESSOA = ?
			AND ID_TAB_APREND = ?
		", array( $barpessoaid, $fg["ID"] ) );

		//SE NAO ATINGIU O MINIMO PARA A AREA
		if ( $fg["MIN_AREA"] > $feitas ):
			marcaRequisitoID( null, $rI->fields["ID"], $fg["ID_TAB_APR_ITEM"] );
		
		//SE ATINGIU O MINIMO PARA A AREA
		else:

			//SE COMPLETOU ALGUMA ESPECIADADE DE CLASSE
			if ($fg["TP_ITEM"] == "CL"):
				if ($dtConclusao >= $rI->fields["DT_INICIO"] || $fg["FG_OBR"] == "S"):
					marcaRequisitoID( $dtConclusao, $rI->fields["ID"], $fg["ID_TAB_APR_ITEM"] );
				endif;

			//SE COMPLETOU ALGUMA ESPECIADADE DE MESTRADO
			elseif ($fg["TP_ITEM"] == "ES"):

				if ($rI->EOF || is_null($rI->fields["DT_CONCLUSAO"]) ):

					//INSERE NOTIFICAÇOES SE NÃO EXISTIR.
					CONN::get()->execute("
						INSERT INTO LOG_MENSAGEM ( ID_ORIGEM, TP, ID_CAD_USUARIO, EMAIL, DH_GERA )
						SELECT ?, 'M', cu.ID, ca.EMAIL, NOW()
							FROM CON_ATIVOS ca
					INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
							WHERE ca.ID_CAD_PESSOA = ?
							AND NOT EXISTS (SELECT 1 FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = 'M' AND ID_CAD_USUARIO = cu.ID)
					", array( $fg["ID"], $barpessoaid, $fg["ID"] ) );
					$logID = CONN::get()->Insert_ID();
					
					updateHistorico( $barpessoaid, $fg["ID"], 
						array(
							"dt_inicio"			=> dateDefaultInicio(getDateNull($dtInicio)),
							"dt_conclusao"		=> "N",
							"dt_avaliacao"		=> "N",
							"dt_investidura"	=> "N"
							), 
						$compras );
						
					$rA = CONN::get()->execute("SELECT * FROM CON_ATIVOS WHERE ID_CAD_PESSOA = ?",array($barpessoaid));
					$a = explode(" ",titleCase($rA->fields["NM"]));
						
          if (!empty($rA->fields["EMAIL"])):
            $mail = MAIL::get();
						$rD = CONN::get()->execute("SELECT * FROM CON_DIRETOR");
						$nomeDiretor = titleCase($rD->fields["NOME_DIRETOR"]);

						$message = new MESSAGE( array( "np" => $a[0], "nm" => $fg["DS_ITEM"], "sx" => $rA->fields["SEXO"], "nd" => $nomeDiretor ) );
					
						$mail->ClearAllRecipients();
						$mail->AddAddress( $rA->fields["EMAIL"] );
						$mail->Subject = utf8_decode(PATTERNS::getClubeDS( array("cl","nm") ) . " - Aviso de Conclusão");
						$mail->MsgHTML( $message->getConclusao() );
							
						if ( $mail->Send() ):
							CONN::get()->execute("UPDATE LOG_MENSAGEM SET DH_SEND = NOW() WHERE ID = ?", array( $logID ) );
						endif;
					endif;
				endif;
			endif;
		endif;
	endforeach;
}

function consultaAprendizadoPessoa( $tabAprendID, $pessoaID ){
	$arr = array( "ap" => "", "ar" => "", "cd" => "", "nm" => "" );
	$rs = CONN::get()->execute("
		SELECT DISTINCT ta.CD_COR, ta.DS_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, tm.NR_PG_ASS
		  FROM TAB_APRENDIZADO ta
	 LEFT JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
		 WHERE ta.ID = ?
	", array( $tabAprendID ) );
	if (!$rs->EOF):
		$arr["cr"] = $rs->fields["CD_COR"];
		$arr["ap"] = $rs->fields["DS_ITEM"];
		$arr["ar"] = $rs->fields["CD_AREA_INTERNO"];
		$arr["cd"] = $rs->fields["CD_ITEM_INTERNO"];
		$arr["pg"] = $rs->fields["NR_PG_ASS"];
	endif;
	
	$rp = CONN::get()->execute("SELECT * FROM CAD_PESSOA WHERE ID = ?", array( $pessoaID ) );
	if (!$rp->EOF):
		$arr["nm"] = ($rp->fields["NM"]);
	endif;
	return $arr;
}

function podeAtualizarDtInvestidura($pessoaID,$aprendID){
	$result = CONN::get()->execute("
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
	
	$res = CONN::get()->execute("
		  SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.DT_INICIO, 
			 MAX(DT_ASSINATURA) AS DT_MAX_ASSINATURA, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		   WHERE ta.ID_CAD_PESSOA = ?
			 AND ta.DT_CONCLUSAO IS NULL
		GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.DT_INICIO
		ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
	", array( $pessoaID ) );

	if ($res->EOF):
		CONN::get()->execute("
			DELETE FROM APR_PESSOA_REQ 
			WHERE ID_HISTORICO IN (SELECT ID FROM APR_HISTORICO WHERE ID_CAD_PESSOA = ?)	
		", array( $pessoaID ) );
	else:
		foreach ($res as $kes => $les):
			$rc = CONN::get()->execute("
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

				$pq = CONN::get()->execute("
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
					CONN::get()->execute("
						UPDATE APR_HISTORICO 
						SET DT_CONCLUSAO = ? 
						WHERE ID = ?
					", array( $les['DT_MAX_ASSINATURA'], $pq->fields['ID'] ) );
					
					$op .= ",UPDATED";
					$alterados++;
				else:
					$op .= ",NOT UPDATED";
				endif;
				if ( $reqok == true ):
					CONN::get()->execute("
						DELETE FROM APR_PESSOA_REQ 
						WHERE ID_HISTORICO IN (SELECT ID FROM APR_HISTORICO WHERE ID_CAD_PESSOA = ? AND ID_TAB_APREND = ?)	
					", array( $pessoaID, $les["ID_TAB_APREND"] ) );
					
					$op .= ",DELETED";
					$deletados++;
				endif;
				$arr[] = "HISTORICO APR[".$les["ID_TAB_APREND"]."],PESSOA[$pessoaID],TOT[".$les["QT_REQ"]."],DONE[$qtdCompl]$op";
			endif;
		endforeach;
	endif;
	return array("del" => $deletados, "upd" => $alterados, "op" => $arr);
}
?>
