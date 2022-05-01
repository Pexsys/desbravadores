<?php
@require_once("../include/functions.php");
@require_once("../include/compras.php");
@require_once("../include/historico.php");
@require_once("../include/tags.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	$where = "";
	$aWhere = array();
	if ( isset($parameters["filters"]) ):
		$keyAnt = "";
		foreach ($parameters["filters"] as $key => $v):
			$not = false;
			if ( isset($parameters["filters"][$key]["fg"]) ):
				$not = strtolower($parameters["filters"][$key]["fg"]) == "true";
			endif;
			$notStr = ( $not ? "NOT " : "" );
			if ( $key == "X" ):
				$where .= " AND ca.TP_SEXO ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND ca.ID_UNIDADE ".$notStr."IN";
			elseif ( $key == "T" ):
				$where .= " AND ca.ID_CAD_MEMBRO ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND ap.TP_ITEM = 'CL' AND ap.ID ".$notStr."IN";
			elseif ( $key == "E" ):
				$where .= " AND ap.TP_ITEM = 'ES' AND ap.CD_AREA_INTERNO <> 'ME' AND ap.ID ".$notStr."IN";
			elseif ( $key == "M" ):
				$where .= " AND ap.TP_ITEM = 'ES' AND ap.CD_AREA_INTERNO = 'ME' AND ap.ID ".$notStr."IN";
      elseif ( $key == "IN" ):
        $where .= " AND ap.TP_ITEM = 'MT' AND ap.CD_AREA_INTERNO = 'TEMPO' AND ap.ID ".$notStr."IN";
        elseif ( $key == "A" ):
				$where .= " AND ap.CD_AREA_INTERNO ".$notStr."IN";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $parameters["filters"][$key]["vl"] ) ):
				foreach ($parameters["filters"][$key]["vl"] as $value):
					if ( $key == "G" ):
						if ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."ca.CD_FANFARRA IS ".( !$not ? "NOT NULL" : "NULL");
						else:
							$where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
						endif;
					elseif ( $key == "IC" ):
						if ( $value == "0" ):
							$where .= (!$prim ? " OR " : "") ."cc.FG_COMPRA = 'S'";
						elseif ( $value == "1" ):
							$where .= (!$prim ? " OR " : "") ."cc.FG_COMPRA = 'N'";
						elseif ( $value == "2" ):
							$where .= (!$prim ? " OR " : "") ."cc.FG_ENTREGUE = 'S'";
						elseif ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."cc.FG_ENTREGUE = 'N'";
						elseif ( $value == "4" ):
							$where .= (!$prim ? " OR " : "") ."cc.FG_PREVISAO = 'S'";
						elseif ( $value == "5" ):
							$where .= (!$prim ? " OR " : "") ."(cc.FG_COMPRA = 'N' AND cc.FG_PREVISAO = 'N')";
						endif;
					elseif ( $key == "HA" ):
						if ( $value == "0" ):
							$where .= (!$prim ? " OR " : "") ."ah.DT_CONCLUSAO IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "1" ):
							$where .= (!$prim ? " OR " : "") ."ah.DT_CONCLUSAO IS NOT NULL AND ah.DT_AVALIACAO IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "2" ):
							$where .= (!$prim ? " OR " : "") ."YEAR(ah.DT_AVALIACAO) ".( !$not ? " = " : " <> ")." YEAR(NOW())";
						elseif ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."ah.DT_CONCLUSAO IS NOT NULL AND ah.DT_AVALIACAO IS NOT NULL AND ah.DT_INVESTIDURA IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "4" ):
							$where .= (!$prim ? " OR " : "") ."YEAR(ah.DT_INVESTIDURA) ".( !$not ? " = " : " <> ")." YEAR(NOW())";
						elseif ( $value == "5" ):
							$where .= (!$prim ? " OR " : "") ."YEAR(ah.DT_AVALIACAO) ".( !$not ? " < " : " <> ")." YEAR(NOW())";
						endif;
					elseif ( $key == "Z" ):
						$where .= (!$prim ? " OR " : "") ."ap.TP_ITEM ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
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
		endforeach;
	endif;



	$query = "
		SELECT DISTINCT
			ah.ID,
			ca.ID_CAD_PESSOA,
			ca.NM,
			ah.DT_INICIO,
			ap.TP_ITEM,
			ap.CD_ITEM_INTERNO,
			ta.DS,
			ap.DS_ITEM
		FROM CON_ATIVOS ca
		INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = ca.ID_CAD_PESSOA AND ah.DT_INVESTIDURA IS NULL)
		INNER JOIN TAB_APRENDIZADO ap ON (ap.id = ah.id_tab_aprend)
		 LEFT JOIN TAB_TP_APRENDIZADO ta ON (ta.id = ap.tp_item)
		 LEFT JOIN CON_COMPRAS cc ON (cc.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND cc.id_tab_aprend = ah.id_tab_aprend)
		WHERE 1=1 $where
	 ORDER BY ca.NM, ap.CD_ITEM_INTERNO, ah.DT_INICIO
	";

	return CONN::get()->execute( $query, $aWhere );
}

function getAprendizado( $parameters ) {
	$arr = array();


	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$arr[] = array(
			"id" => $fields['ID'],
			"ip" => $fields['ID_CAD_PESSOA'],
			"nm" => ($fields['NM']),
			"dsitp" => ($fields['DS']),
			"dsitm" => ($fields['DS_ITEM']) . ($fields['TP_ITEM'] == "ES" ? " - ".$fields['CD_ITEM_INTERNO'] : "")
		);
	endforeach;

	return array( "result" => true, "aprendizado" => $arr );
}

function delete( $parameters ) {
	$ids = $parameters["ids"];
	$compras = new COMPRAS();

	foreach ($ids as $k => $id):
		$rs = CONN::get()->execute("SELECT ID_CAD_PESSOA, ID_TAB_APREND FROM APR_HISTORICO WHERE ID = ?", array($id) );
		if (!$rs->EOF):
			$compras->deleteByPessoa($rs->fields["ID_CAD_PESSOA"]);

            //REMOVE NOTIFICACOES, SE EXISTIREM.
            CONN::get()->execute("
    		    DELETE FROM LOG_MENSAGEM
    		    WHERE ID_ORIGEM = ? AND TP = ? AND ID_CAD_USUARIO = (SELECT ID FROM CAD_USUARIO WHERE ID_CAD_PESSOA = ?)
    		", array( $rs->fields["ID_TAB_APREND"], "M", $rs->fields["ID_CAD_PESSOA"] ) );
		endif;

		$rs = CONN::get()->execute("
			SELECT apr.ID
			FROM APR_HISTORICO ah
			INNER JOIN TAB_APR_ITEM_SEL tais ON (tais.ID_REF = ah.ID_TAB_APREND)
			INNER JOIN TAB_APR_ITEM tai ON (tai.ID = tais.ID_TAB_APR_ITEM)
			INNER JOIN APR_PESSOA_REQ apr ON (apr.ID_TAB_APR_ITEM = tais.ID_TAB_APR_ITEM)
			INNER JOIN APR_HISTORICO ah2 ON (ah2.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND ah2.ID = apr.ID_HISTORICO AND ah2.DT_CONCLUSAO IS NULL)
			WHERE ah.ID = ?
		", array($id) );
		foreach ($rs as $k => $f):
			CONN::get()->execute("DELETE FROM APR_PESSOA_REQ WHERE ID = ?", array($f["ID"]) );
		endforeach;

		CONN::get()->execute("DELETE FROM APR_PESSOA_REQ WHERE ID_HISTORICO = ?", array($id) );
		CONN::get()->execute("DELETE FROM APR_HISTORICO WHERE ID = ?", array($id) );

	endforeach;
	return array( "result" => true );
}

function setAprendizado( $parameters ) {
	$tags = new TAGS();
	$compras = new COMPRAS();

	$arr = array();

	$frm = $parameters["frm"];

	//ATRIBUTOS PARA INCLUSAO/ATUALIZACAO
	$paramDates = getParamDates( $frm );

	//UNIFICACAO DE ITENS DE APRENDIZADO.
	$arrItens = array();

	//SE TEM CLASSES PARA ADICIONAR/ATUALIZAR
	if (isset($frm["cd_classe"]) && is_array($frm["cd_classe"]) ):
		$arrItens = array_merge($arrItens, $frm["cd_classe"]);
	endif;

	//SE TEM ESPECIALIDADE PARA ADICIONAR/ATUALIZAR
	if (isset($frm["cd_espec"]) && is_array($frm["cd_espec"]) ):
		$arrItens = array_merge($arrItens, $frm["cd_espec"]);
	endif;

	//SE TEM MERITO PARA ADICIONAR/ATUALIZAR
	if (isset($frm["cd_merito"]) && is_array($frm["cd_merito"]) ):
		$arrItens = array_merge($arrItens, $frm["cd_merito"]);
	endif;

	//SE TEM MESTRADO PARA ADICIONAR/ATUALIZAR
	if (isset($frm["cd_mest"]) && is_array($frm["cd_mest"]) ):
		$arrItens = array_merge($arrItens, $frm["cd_mest"]);
	endif;

	if ( $paramDates["dt_inicio"] != "N" || $paramDates["dt_conclusao"] != "N" || $paramDates["dt_avaliacao"] != "N" || $paramDates["dt_investidura"] != "N" ):

		//VERIFICA SE TEM IDS SELECIONADOS PARA ATUALIZAR ATRIBUTOS.
		if ( isset($frm["id"]) && is_array($frm["id"]) ):
		    $arr["x"] = array();
			$frm["id_pessoa"] = array();
			$prepare = updateHistoricoQuery( $paramDates, "ID = ?" );

			foreach ($frm["id"] as $id):
				$paramDates = getParamDates( $frm );
				$rh = CONN::get()->execute("SELECT ID_CAD_PESSOA, ID_TAB_APREND FROM APR_HISTORICO WHERE ID = ?", array($id));
				if (!$rh->EOF):
					if ( !in_array($rh->fields["ID_CAD_PESSOA"],$frm["id_pessoa"]) ):
						$frm["id_pessoa"][] = $rh->fields["ID_CAD_PESSOA"];
					endif;
					if (!podeAtualizarDtInvestidura($rh->fields["ID_CAD_PESSOA"],$rh->fields["ID_TAB_APREND"]) ):
						$paramDates["dt_investidura"] = null;
						$prepare = updateHistoricoQuery( $paramDates, "ID = ?" );
					endif;

					$tmp = $prepare["bind"];
					$tmp[] = $id;
					CONN::get()->execute( $prepare["query"], $tmp );

					if (!is_null($paramDates["dt_investidura"])):
						$compras->deleteItemPessoaEntregue( $rh->fields["ID_CAD_PESSOA"], $rh->fields["ID_TAB_APREND"] );
					endif;
				endif;
			endforeach;

		//VERIFICA SE TEM MEMBROS ESPECIFICOS PARA ATUALIZAR ATRIBUTOS,
		//DESDE QUE NAO TENHA ITENS DE APRENDIZADO A INSERIR/ALTERAR ESPECIFICOS
		elseif ( isset($frm["id_pessoa"]) && is_array($frm["id_pessoa"]) && count($arrItens) == 0 ):
		    $arr["x"] = array();
			foreach ($frm["id_pessoa"] as $id):
				$rh = CONN::get()->execute("SELECT ID_TAB_APREND FROM APR_HISTORICO WHERE ID_CAD_PESSOA = ? AND DT_INVESTIDURA IS NULL", array($id));
				if (!$rh->EOF):
					foreach ($rh as $k => $ln):
						$paramDates = getParamDates( $frm );
						if ( !podeAtualizarDtInvestidura($id, $ln["ID_TAB_APREND"]) ):
							$paramDates["dt_investidura"] = null;
						endif;

						$prepare = updateHistoricoQuery( $paramDates, "ID_CAD_PESSOA = ?" );
						$tmp = $prepare["bind"];
						$tmp[] = $id;
						CONN::get()->execute( $prepare["query"], $tmp );

						if (!is_null($paramDates["dt_investidura"])):
							$compras->deleteItemPessoaEntregue( $id, $ln["ID_TAB_APREND"] );
						endif;

					endforeach;
				endif;
			endforeach;
		endif;
	endif;

	$arr["result"] = true;
	$arr["msg"] = "";

	//INCLUI/ATUALIZA ITENS
	foreach ($arrItens as $id):
		foreach ($frm["id_pessoa"] as $idPessoa):

		    $deixa = true;
		    //VERIFICA SE ITEM É UM MESTRADO E SE COMPLETOU TODOS AS ESPECIALIDADES DA REGRA.
		    if ( isset($frm["cd_mest"]) && is_array($frm["cd_mest"]) && in_array($id, is_array($frm["cd_mest"])) ):

		        //LE REGRAS
            	$rg = CONN::get()->execute("
            	    SELECT DISTINCT car.ID, car.CD_ITEM_INTERNO, car.CD_AREA_INTERNO, car.DS_ITEM, car.TP_ITEM, car.MIN_AREA
            	      FROM CON_APR_REQ car
            	     WHERE car.ID = ?
            	", array($id) );
            	foreach ($rg as $lg => $fg):
                    $min = $fg["MIN_AREA"];

                    $feitas = 0;
                    //LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
            	    $rR = CONN::get()->execute("
                            SELECT tar.ID, tar.QT_MIN, COUNT(*) AS QT_FEITAS
                              FROM TAB_APR_ITEM tar
                        INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_ITEM = tar.ID AND car.TP_ITEM_RQ = 'ES')
                        INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
                             WHERE tar.ID_TAB_APREND = ?
                          GROUP BY tar.ID, tar.QT_MIN
                	", array($idPessoa, $fg["ID"]) );
            	    foreach($rR as $lR => $fR):
                        $feitas += min( $fR["QT_MIN"], $fR["QT_FEITAS"] );
                    endforeach;

            		$pct = floor( ( $feitas / $min ) * 100 );

            		//VERIFICA SE AINDA NÃO CONCLUIDO
            		if ( $pct < 100 ):
                        $arr["result"] = false;
                        $arr["msg"] .= "Verifique regra. Inclusão não permitida para o item ".$fg["DS_ITEM"];
                        $deixa = false;
            		endif;
            	endforeach;
            endif;

		    if ($deixa):
				updateHistorico( $idPessoa, $id, $paramDates, $compras );
				analiseHistoricoPessoa($idPessoa);
			endif;

			//INCLUI ITENS DE IDENTIFICACAO
			if ($id >= 1 && $id <= 12):
				if (isset($frm["tp_tag"]) && is_array($frm["tp_tag"]) ):
					$rp = CONN::get()->execute("
							SELECT ID
							  FROM CAD_MEMBRO
							 WHERE ID_CAD_PESSOA = ?
							   AND ID_CLUBE = ?
					", array( $idPessoa, PATTERNS::getBars()->getClubeID() ) );
					if (!$rp->EOF):
						foreach ($frm["tp_tag"] as $tp):
							$tags->insertItemTag( $tp, $rp->fields["ID"], $id );
						endforeach;
					endif;
				endif;
			endif;

		endforeach;
	endforeach;

	return $arr;
}

function getData(){
	$arr = array();
	$arr["result"] = false;
	$arr["nomes"] = array();

	$qtdZeros = zeroSizeID();
	$result = CONN::get()->execute("SELECT ID_CAD_PESSOA, ID_MEMBRO, NM FROM CON_ATIVOS ORDER BY NM");
	while (!$result->EOF):
		$arr["nomes"][] = array(
			"id" => $result->fields['ID_CAD_PESSOA'],
			"ds" => $result->fields['NM'],
			"sb" => fStrZero($result->fields['ID_MEMBRO'], $qtdZeros)
		);
		$result->MoveNext();
	endwhile;

	$arr["classe"] = getClasse();
	$arr["especialidade"] = getEspecialidade();
	$arr["mestrado"] = getMestrado();
	$arr["merito"] = getMerito();
	$arr["tags"] = PATTERNS::getBars()->getTagsTipo("tg","S");

	return $arr;
}

function getMerito(){
	$arr = array();
	$result = CONN::get()->execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO
		 WHERE TP_ITEM = ?
	  ORDER BY CD_ITEM_INTERNO", array( "MT" ) );
	foreach ($result as $k => $line):
		$arr[] = array(
			"id"	=> $line['ID'],
			"ds"	=> $line['DS_ITEM']
		);
	endforeach;
	return $arr;
}

function getClasse(){
	$arr = array();
	$result = CONN::get()->execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO
		 WHERE TP_ITEM = 'CL'
	  ORDER BY CD_ITEM_INTERNO" );
	foreach ($result as $k => $line):
		$arr[] = array(
			"id"	=> $line['ID'],
			"ds"	=> $line['DS_ITEM']
		);
	endforeach;
	return $arr;
}

function getMestrado(){
	$arr = array();
	$result = CONN::get()->execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO
		 WHERE TP_ITEM = 'ES'
		   AND CD_AREA_INTERNO = 'ME'
		   AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY DS_ITEM");
	foreach ($result as $k => $line):
		$arr[] = array(
			"id"	=> $line['ID'],
			"ds"	=> $line['DS_ITEM'],
			"sb"	=> $line['CD_ITEM_INTERNO']
		);
	endforeach;
	return $arr;
}

function getEspecialidade(){
	$arr = array();
	$result = CONN::get()->execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO
		 WHERE TP_ITEM = 'ES'
		   AND CD_AREA_INTERNO <> 'ME'
		   AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY DS_ITEM" );
	foreach ($result as $k => $line):
		$arr[] = array(
			"id"	=> $line['ID'],
			"ds"	=> $line['DS_ITEM'],
			"sb"	=> $line['CD_ITEM_INTERNO']
		);
	endforeach;
	return $arr;
}
?>
