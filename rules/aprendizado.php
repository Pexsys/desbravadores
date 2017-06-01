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
				$where .= " AND at.TP_SEXO ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND at.ID_UNIDADE ".$notStr."IN";
			elseif ( $key == "T" ):
				$where .= " AND at.ID ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND ap.TP_ITEM = ? AND ap.ID ".$notStr."IN";
				$aWhere[] = "CL";
			elseif ( $key == "E" ):
				$where .= " AND ap.TP_ITEM = ? AND ap.CD_AREA_INTERNO <> ? AND ap.ID ".$notStr."IN";
				$aWhere[] = "ES";
				$aWhere[] = "ME";
			elseif ( $key == "M" ):
				$where .= " AND ap.TP_ITEM = ? AND ap.CD_AREA_INTERNO = ? AND ap.ID ".$notStr."IN";
				$aWhere[] = "ES";
				$aWhere[] = "ME";
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
							$where .= (!$prim ? " OR " : "") ."at.CD_FANFARRA IS ".( !$not ? "NOT NULL" : "NULL");
						else:
							$where .= (!$prim ? " OR " : "") ."at.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
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

//echo $where;
//exit;

	$query = "
		SELECT DISTINCT
			ah.ID,
			at.ID as ID_CAD_PESSOA,
			at.NM,
			ah.DT_INICIO,
			ap.TP_ITEM,
			ap.CD_ITEM_INTERNO,
			ta.DS,
			ap.DS_ITEM
		FROM CON_ATIVOS at
		INNER JOIN APR_HISTORICO ah ON (ah.id_cad_pessoa = at.id AND ah.DT_INVESTIDURA IS NULL)
		INNER JOIN TAB_APRENDIZADO ap ON (ap.id = ah.id_tab_aprend)
		INNER JOIN TAB_TP_APRENDIZADO ta ON (ta.id = ap.tp_item)
		 LEFT JOIN CON_COMPRAS cc ON (cc.id_cad_pessoa = ah.id_cad_pessoa AND cc.id_tab_aprend = ah.id_tab_aprend)
		WHERE 1=1 $where
	 ORDER BY at.NM, ap.CD_ITEM_INTERNO, ah.DT_INICIO
	";

	return $GLOBALS['conn']->Execute( $query, $aWhere );
}

function getAprendizado( $parameters ) {
	$arr = array();
	
	fConnDB();
	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$arr[] = array( 
			"id" => $fields['ID'],
			"ip" => $fields['ID_CAD_PESSOA'],
			"nm" => utf8_encode($fields['NM']),
			"dsitp" => utf8_encode($fields['DS']),
			"dsitm" => utf8_encode($fields['DS_ITEM']) . ($fields['TP_ITEM'] == "ES" ? " - ".$fields['CD_ITEM_INTERNO'] : "")
		);
	endforeach;
	
	return array( "result" => true, "aprendizado" => $arr );
}

function delete( $parameters ) {
	$ids = $parameters["ids"];
	
	fConnDB();
	foreach ($ids as $k => $id):
		$rs = $GLOBALS['conn']->Execute("SELECT ID_CAD_PESSOA, ID_TAB_APREND FROM APR_HISTORICO WHERE ID = ?", array($id) );
		if (!$rs->EOF):
			$GLOBALS['conn']->Execute("
				DELETE FROM CAD_COMPRAS_PESSOA 
				 WHERE ID_CAD_PESSOA = ? 
				   AND ID_TAB_APREND = ?
			", array( $rs->fields["ID_CAD_PESSOA"], $rs->fields["ID_TAB_APREND"]) );
		endif;
		$GLOBALS['conn']->Execute("DELETE FROM APR_HISTORICO WHERE ID = ?", array($id) );
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
	fConnDB();
	
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
			$frm["id_pessoa"] = array();
			$prepare = updateHistoricoQuery( $paramDates, "ID = ?" );

			foreach ($frm["id"] as $id):
				$paramDates = getParamDates( $frm );
				$rh = $GLOBALS['conn']->Execute("SELECT ID_CAD_PESSOA, ID_TAB_APREND FROM APR_HISTORICO WHERE ID = ?", array($id));
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
					$GLOBALS['conn']->Execute( $prepare["query"], $tmp );		

					if (!is_null($paramDates["dt_investidura"])):
						$compras->deleteItemPessoaEntregue( $rh->fields["ID_CAD_PESSOA"], $rh->fields["ID_TAB_APREND"] );
					endif;
				endif;
			endforeach;
			
		//VERIFICA SE TEM MEMBROS ESPECIFICOS PARA ATUALIZAR ATRIBUTOS,
		//DESDE QUE NAO TENHA ITENS DE APRENDIZADO A INSERIR/ALTERAR ESPECIFICOS
		elseif ( isset($frm["id_pessoa"]) && is_array($frm["id_pessoa"]) && count($arrItens) == 0 ):
			foreach ($frm["id_pessoa"] as $id):
				$rh = $GLOBALS['conn']->Execute("SELECT ID_TAB_APREND FROM APR_HISTORICO WHERE ID_CAD_PESSOA = ? AND DT_INVESTIDURA IS NULL ", array($id));
				if (!$rh->EOF):
					foreach ($rh as $k => $ln):
						$paramDates = getParamDates( $frm );
						if ( !podeAtualizarDtInvestidura($id, $ln["ID_TAB_APREND"]) ):
							$paramDates["dt_investidura"] = null;
						endif;
						
						$prepare = updateHistoricoQuery( $paramDates, "ID_CAD_PESSOA = ?" );
						$tmp = $prepare["bind"];
						$tmp[] = $id;
						$GLOBALS['conn']->Execute( $prepare["query"], $tmp );
						
						if (!is_null($paramDates["dt_investidura"])):
							$compras->deleteItemPessoaEntregue( $id, $ln["ID_TAB_APREND"] );
						endif;
						
					endforeach;
				endif;
			endforeach;
		endif;
	endif;

	//INCLUI/ATUALIZA ITENS
	foreach ($arrItens as $id):
		foreach ($frm["id_pessoa"] as $idPessoa):
			updateHistorico( $idPessoa, $id, $paramDates, $compras );
			
			//INCLUI ITENS DE IDENTIFICACAO
			if ($id >= 1 && $id <= 12):
				if (isset($frm["tp_tag"]) && is_array($frm["tp_tag"]) ):
					foreach ($frm["tp_tag"] as $tp):
						$tags->insertItemTag( $tp, $idPessoa, $id );
					endforeach;
				endif;
			endif;
			
		endforeach;
	endforeach;

	$arr["result"] = true;
	return $arr;
}

function getData(){
	$arr = array();
	$arr["result"] = false;
	$arr["nomes"] = array();
	
	fConnDB();
	$qtdZeros = zeroSizeID();
	$result = $GLOBALS['conn']->Execute("SELECT ID, NM FROM CON_ATIVOS ORDER BY NM");
	while (!$result->EOF):
		$id = str_pad($result->fields['ID'], $qtdZeros, "0", STR_PAD_LEFT);
		$arr["nomes"][] = array( 
			"id" => $id,
			"ds" => "$id ".utf8_encode($result->fields['NM'])
		);
		$result->MoveNext();
	endwhile;
	
	$arr["classe"] = getClasse();
	$arr["especialidade"] = getEspecialidade();
	$arr["mestrado"] = getMestrado();
	$arr["merito"] = getMerito();
	$arr["tags"] = getTagsTipo();
	
	return $arr;
}

function getMerito(){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO 
		 WHERE TP_ITEM = ?
	  ORDER BY CD_ITEM_INTERNO", array( "MT" ) );
	foreach ($result as $k => $line):
		$arr[] = array( 
			"id"	=> $line['ID'],
			"ds"	=> utf8_encode($line['DS_ITEM'])
		);
	endforeach;
	return $arr;
}

function getClasse(){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO 
		 WHERE TP_ITEM = ?
	  ORDER BY CD_ITEM_INTERNO", array( "CL" ) );
	foreach ($result as $k => $line):
		$arr[] = array( 
			"id"	=> $line['ID'],
			"ds"	=> utf8_encode($line['DS_ITEM'])
		);
	endforeach;
	return $arr;
}

function getMestrado(){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO 
		 WHERE TP_ITEM = ?
		   AND CD_AREA_INTERNO = ?
		   AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY CD_ITEM_INTERNO, DS_ITEM", array( "ES", "ME" ) );
	foreach ($result as $k => $line):
		$arr[] = array( 
			"id"	=> $line['ID'],
			"ds"	=> $line['CD_ITEM_INTERNO'] ." ". utf8_encode($line['DS_ITEM'])
		);
	endforeach;
	return $arr;
}

function getEspecialidade(){
	$arr = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT ID, CD_ITEM_INTERNO, DS_ITEM
		  FROM TAB_APRENDIZADO 
		 WHERE TP_ITEM = ?
		   AND CD_AREA_INTERNO <> ?
		   AND CD_ITEM_INTERNO IS NOT NULL
	  ORDER BY CD_ITEM_INTERNO, DS_ITEM", array( "ES", "ME" ) );
	foreach ($result as $k => $line):
		$arr[] = array( 
			"id"	=> $line['ID'],
			"ds"	=> $line['CD_ITEM_INTERNO'] ." ". utf8_encode($line['DS_ITEM'])
		);
	endforeach;
	return $arr;
}
?>