<?php
@require_once("../include/functions.php");
@require_once("../include/compras.php");
@require_once("../include/materiais.php");
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
						elseif ( $value == "4" ):
						    $where .= (!$prim ? " OR " : "") ."at.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-04%'");
						elseif ( $value == "5" ):
						    $where .= (!$prim ? " OR " : "") ."at.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '2-07%'");
						elseif ( $value == "6" ):
						    $where .= (!$prim ? " OR " : "") ."at.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '1-01%'");
						else:
							$where .= (!$prim ? " OR " : "") ."at.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
						endif;
					elseif ( $key == "IC" ):
						if ( $value == "0" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_COMPRA = 'S'";
						elseif ( $value == "1" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_COMPRA = 'N'";
						elseif ( $value == "2" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_ENTREGUE = 'S'";
						elseif ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."ccp.FG_ENTREGUE = 'N'";
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
		SELECT 
			ccp.ID,
			ccp.COMPL AS CM,
			ccp.TP AS TP_INCL,
			ccp.FG_COMPRA,
			ccp.FG_ENTREGUE,
			tm.TP,
			tm.DS,
			tm.FUNDO,
			tm.CMPL,
			tm.FG_IM,
			at.NM,
			ap.TP_ITEM,
			ap.DS_ITEM
		FROM CAD_COMPRAS_PESSOA ccp
		INNER JOIN CON_ATIVOS at ON (at.ID = ccp.id_cad_pessoa)
		INNER JOIN TAB_MATERIAIS tm ON (tm.id = ccp.id_tab_materiais)
		 LEFT JOIN TAB_APRENDIZADO ap ON (ap.id = tm.id_tab_aprend)
		WHERE 1=1 $where
	 ORDER BY at.NM, tm.CD, ccp.COMPL
	";

	return $GLOBALS['conn']->Execute( $query, $aWhere );
}

function getLista( $parameters ) {
	$arr = array();
	
	fConnDB();
	$result = getQueryByFilter( $parameters );
	
	foreach ($result as $k => $fields):
		$ds = $fields['TP'] ." DE ". $fields['DS'];
	
		if ( $fields['CMPL'] == "S" && $fields['FG_IM'] == 'N'):
			$ds .= " - ". $fields['DS_ITEM'];
		endif;
		
		if ( !empty($fields['FUNDO']) ):
			$ds .= " - FUNDO ". ($fields['FUNDO'] == "BR" ?  "BRANCO" : "CAQUI");
		endif;

		if ( !empty($fields['CM']) ):
			$ds .= " [".$fields['CM']."]";
		endif;

		$arr[] = array( 
			"tp" => $fields['TP_INCL'],
			"id" => $fields['ID'],
			"nm" => $fields['NM'],
			"ds" => $ds,
			"ic" => $fields['FG_COMPRA'],
			"ie" => $fields['FG_ENTREGUE']
		);
	endforeach;

	return array( "result" => true, "compras" => $arr );
}

function process(){
	fConnDB();
	
	$compras = new COMPRAS();

	//SELECIONA PESSOAS POR ORDEM ALFABETICA
	$result = $GLOBALS['conn']->Execute("
		    SELECT DISTINCT ca.ID
		      FROM APR_HISTORICO ah
		INNER JOIN CON_ATIVOS ca ON (ca.ID = ah.ID_CAD_PESSOA)
		     WHERE ah.DT_AVALIACAO IS NOT NULL 
		       AND ah.DT_INVESTIDURA IS NULL
		  ORDER BY ca.NM
	");
	foreach ($result as $k => $ls):
		$compras->processaListaPessoaID( $ls['ID'], "A" );
	endforeach;
	
	return array( "result" => true );
}

function gerarPDF(){
	return array( "result" => true );
}

function getData( $parameters ) {
	$arr = array();
	
	fConnDB();
	foreach ($parameters["domains"] as $y => $f):
		if ( $f == "tipos" ):
			$arr["tipos"] = array();
			$result = $GLOBALS['conn']->Execute("
				SELECT DISTINCT TP 
				  FROM TAB_MATERIAIS 
				 WHERE FG_IM = 'S'
				 ORDER BY TP");
			foreach ($result as $k => $fields):
				$tp = ($fields['TP']);
				$arr["tipos"][] = array( 
					"id" => $tp,
					"ds" => $tp
				);
			endforeach;
		endif;
		if ( $f == "tiposEst" ):
			$arr["tipos"] = array();
			$result = $GLOBALS['conn']->Execute("
				SELECT DISTINCT TP 
				  FROM TAB_MATERIAIS 
				 ORDER BY TP");
			foreach ($result as $k => $fields):
				$tp = ($fields['TP']);
				$arr["tipos"][] = array( 
					"id" => $tp,
					"ds" => $tp
				);
			endforeach;
		endif;
		if ( $f == "nomes" ):
			$arr["nomes"] = array();
			$qtdZeros = zeroSizeID();
			$result = $GLOBALS['conn']->Execute("SELECT ID, NM FROM CON_ATIVOS ORDER BY NM");
			foreach ($result as $k => $fields):
				$id = fStrZero($fields['ID'], $qtdZeros);
				$arr["nomes"][] = array( 
					"id" => $id,
					"ds" => "$id ".$fields['NM']
				);
			endforeach;
		endif;
		if ( $f == "itens" ):
			$arr["itens"] = array();
			$result = $GLOBALS['conn']->Execute("
				SELECT ID, DS, CMPL, FUNDO
				  FROM TAB_MATERIAIS 
				 WHERE FG_IM = 'S' AND TP = ?
				ORDER BY DS
			", array($parameters["key"]));
			foreach ($result as $k => $fields):
				$arr["itens"][] = array( 
					"id" => $fields['ID'],
					"cm" => $fields['CMPL'],
					"ds" => $fields['DS'],
					"sb" => ( !is_null($fields['FUNDO']) ? "FUNDO ". ($fields['FUNDO'] == "BR" ? "BRANCO" : "CAQUI") : "")
				);
			endforeach;
		endif;
		if ( $f == "itensEst" ):
			$arr["itens"] = array();
			$result = $GLOBALS['conn']->Execute("
				SELECT tm.ID, tm.DS, tm.CMPL, tm.FUNDO, ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM
				  FROM TAB_MATERIAIS tm
			 LEFT JOIN TAB_APRENDIZADO ta ON (ta.ID = tm.ID_TAB_APREND)
				 WHERE tm.TP = ?
				   AND tm.FG_IM <> 'G'
				   AND (ta.CD_ITEM_INTERNO IS NOT NULL OR tm.ID_TAB_APREND IS NULL)
				ORDER BY tm.FUNDO DESC, ta.CD_ITEM_INTERNO, tm.DS
			", array($parameters["key"]));
			foreach ($result as $k => $fields):
				$sb = "";
				$ds = $fields['DS'];
				
				if (!is_null($fields['FUNDO'])):
					$sb =  "FUNDO ". ($fields['FUNDO'] == "BR" ? "BRANCO" : "CAQUI");
				endif;
			    if ($fields["TP_ITEM"] == "ES"):
					$ds = $fields["DS"] ." ". $fields["DS_ITEM"];
					$sb = $fields["CD_ITEM_INTERNO"];
			    endif;
				$arr["itens"][] = array( 
					"id" => $fields['ID'],
					"cm" => $fields['CMPL'],
					"ds" => $ds,
					"sb" => $sb
				);
			endforeach;
		endif;
		if ( $f == "nomesEntrega" ):
			$arr["nomes"] = array();
			$qtdZeros = zeroSizeID();
			$result = $GLOBALS['conn']->Execute("
				SELECT DISTINCT ID_CAD_PESSOA, NM 
				  FROM CON_COMPRAS 
				 WHERE FG_COMPRA = 'S' 
				   AND FG_ENTREGUE = 'N' 
			  ORDER BY NM
			");
			foreach ($result as $k => $fields):
				$id = fStrZero($fields['ID_CAD_PESSOA'], $qtdZeros);
				$arr["nomes"][] = array( 
					"id" => $fields['ID_CAD_PESSOA'],
					"ds" => "$id ".$fields['NM']
				);
			endforeach;
		endif;
	endforeach;
	
	return $arr;
}

function delete( $parameters ) {
	$ids = $parameters["ids"];
	
	fConnDB();
	$compras = new COMPRAS();
	foreach ($ids as $k => $id):
		$compras->deleteByID($id);
	endforeach;
	return array( "result" => true );
}

function addCompras( $parameters ) {
	$act = $parameters["act"];
	$frm = $parameters["frm"];
	$cmpl = $frm["cmpl"];
	$qtItens = max( $frm["qt_itens"], 1 );

	//ADICIONA ITENS
	if ( $act == "ADD" && isset($frm["id"]) ):
		fConnDB();
		$compras = new COMPRAS();
		
		if ( isset($frm["id_pessoa"]) ):
			foreach ($frm["id_pessoa"] as $k => $pessoaID):
				for ($qtd=1;$qtd<=$qtItens;$qtd++):
					$cm = $cmpl;
					if ($qtItens>1):
						$cm = (is_null($cmpl) ? "" : $cmpl) ."$qtd/$qtItens";
					endif;
					$compras->forceInsert(
						array(
							fReturnNumberNull($pessoaID),
							$frm["id"],
							"M",
							fReturnStringNull( $cm ),
							"N"
						) 
					);
				endfor;
			endforeach;
		else:
			for ($qtd=1;$qtd<=$qtItens;$qtd++):
				$cm = $cmpl;
				if ($qtItens>1):
					$cm = (is_null($cmpl) ? "" : $cmpl) ."$qtd/$qtItens";
				endif;
				$compras->forceInsert(
					array(
						null,
						$frm["id"],
						"M",
						fReturnStringNull( $cm ),
						"N"
					) 
				);
			endfor;
		endif;

	//SETAR ITENS ENTREGUES POR PESSOA
	elseif ( $act == "SET" && isset($frm["id_pessoa"]) ):
		fConnDB();
		foreach ($frm["id_pessoa"] as $k => $pessoaID):
			$result = $GLOBALS['conn']->Execute("
				SELECT *
				FROM CON_COMPRAS
				WHERE FG_COMPRA = 'S'
				AND FG_ENTREGUE = 'N'
				AND ID_CAD_PESSOA = ?
			", array($pessoaID) );
			foreach ($result as $k => $ln):
				updateEstoque( $ln, "fg_entregue", "S" );
			endforeach;
		endforeach;
	endif;
	
	return array("result" => true);
}

function getAttrPerm( $parameters ) {
	$id = $parameters["id"];
	
	fConnDB();
	$result = $GLOBALS['conn']->Execute("
		SELECT 1
		FROM CON_COMPRAS 
		WHERE ID = ?
		  AND (QT_EST > 0 OR FG_COMPRA = 'S' OR (FG_COMPRA = 'N' AND TP_INCL = 'M'))
	", array($parameters["id"]) );
	$edit = (!$result->EOF);
	
	return array("result" => true, "edit" => $edit );
}

function getAttr( $parameters ) {
	$id = $parameters["id"];
	$arr = array(
		"fg_compra" => "N",
		"fg_entregue" => "N"
	);
	
	fConnDB();
	$result = $GLOBALS['conn']->Execute("
		SELECT FG_COMPRA, FG_ENTREGUE, QT_EST
		FROM CON_COMPRAS 
		WHERE ID = ?
	", array($parameters["id"]) );
	if (!$result->EOF):
		$arr["fg_compra"] = $result->fields["FG_COMPRA"];
		$arr["fg_entregue"] = $result->fields["FG_ENTREGUE"];
		$arr["qt_est"] = $result->fields["QT_EST"];
	endif;
	
	return array( "result" => true, "attr" => $arr );
}

function setAttr( $parameters ) {
	$id = $parameters["id"];
	$fd = $parameters["fd"];
	$vl = $parameters["vl"];
	$qt = array( "qt" => 0 );
	
	fConnDB();
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CON_COMPRAS
		WHERE ID = ?
		  AND (QT_EST > 0 OR FG_COMPRA = 'S' OR (FG_COMPRA = 'N' AND TP_INCL = 'M'))
	", array( $parameters["id"]) );
	if (!$result->EOF):
		$qt = updateEstoque( $result->fields, $fd, $vl );
	endif;

	return array( "result" => true, "est" => $qt );
}

function updateEstoque( $ln, $fd, $vl ){
	$arr = array( "qt" => $ln["QT_EST"] );

	$update = true;
	if ($fd == "fg_compra"):
		$update = false;
		
		//VERIFICA ATUAL
		$movEstoque = $ln["FG_COMPRA"] == "S" ? +1 : -1;
				
		$re = $GLOBALS['conn']->Execute("
			SELECT QT_EST
			  FROM CON_COMPRAS
			 WHERE ID = ?
		", array( $ln["ID"]) );
		if (!$re->EOF):
			$arr["qt"] = $re->fields["QT_EST"];
		endif;

		//PARA ITENS SEM ESTOQUE / TRUNFOS / MEDALHAS / EVENTOS DIVERSOS
		if ($ln["TP_INCL"] == "M" && $ln["FG_ALMOX"] == "N"):
			$update = true;
			
		//PARA ITENS COM ESTOQUE / COMPRAS NO ALMOXARIFADO
		elseif ($arr["qt"] > 0 || $movEstoque > 0):
			$GLOBALS['conn']->Execute("UPDATE TAB_MATERIAIS SET QT_EST = ? WHERE ID = ?
			", array( $arr["qt"] + $movEstoque, $ln["ID_TAB_MATERIAIS"] ) );
			$arr["qt"] += $movEstoque;
			$update = true;
		
		endif;
	endif;

	if ($update):
	    
	    //SE MATERIAL FOI ENTREGUE
		if ( $fd == "fg_entregue" && $vl == "S"):

			//EXCLUI SE INCLUSAO MANUAL.
			if ($ln["TP_INCL"] == "M"):
				$GLOBALS['conn']->Execute("DELETE FROM CAD_COMPRAS_PESSOA WHERE ID = ?", array( $ln["ID"] ) );
				
			else:
				updateCADCompras( $fd, array( $vl, $ln["ID"] ) );
			endif;
		    
			//INSERE LOG DE ENTREGA DE MATERIAIS
			if ($ln["FG_LOG_MATERIAL"] == "S"):
				$materiais = new MATERIAIS();
				$materiais->forceInsert( array( $ln["ID_CAD_PESSOA"], $ln["ID_TAB_MATERIAIS"], date("Y-m-d") ) );
				$arr["close"] = "S";
			endif;

		else:
			updateCADCompras( $fd, array( $vl, $ln["ID"] ) );
    	endif;
	endif;
	return $arr;
}

function updateCADCompras($fd, $arr){
	$GLOBALS['conn']->Execute("
		UPDATE CAD_COMPRAS_PESSOA 
		SET $fd = ?
		WHERE ID = ?
	", $arr );
}

function distribuirEstoque(){
	fConnDB();
	
	//MOVIMENTAR ITENS COMPRADOS E NAO ENTREGUES PARA O ESTOQUE
	$result = $GLOBALS['conn']->Execute("
		SELECT ID_TAB_MATERIAIS, COUNT(*) AS QT_ATTR
		  FROM CON_COMPRAS
		WHERE FG_COMPRA = 'S'
		  AND FG_ENTREGUE = 'N'
		GROUP BY TP_ITEM, ID_TAB_APREND, ID_TAB_MATERIAIS
	");
	foreach ($result as $k => $ln):
		$matID = $ln["ID_TAB_MATERIAIS"];
	
		$GLOBALS['conn']->Execute("UPDATE TAB_MATERIAIS SET QT_EST = QT_EST + ? WHERE ID = ?
			", array( $ln["QT_ATTR"], $matID ) );
		
		$GLOBALS['conn']->Execute("
			UPDATE CAD_COMPRAS_PESSOA 
			  SET FG_COMPRA = 'N'
			 WHERE ID_TAB_MATERIAIS = ?
			   AND FG_COMPRA = 'S'
			   AND FG_ENTREGUE = 'N'
		", array($matID) );
	endforeach;
	
	//DISTRIBUIR ITENS DO ESTOQUE
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CON_COMPRAS
		 WHERE QT_EST > 0
		   AND FG_COMPRA = 'N'
		   AND FG_ENTREGUE = 'N'
	  ORDER BY DT_NASC DESC, CD_ITEM_INTERNO, NM
	");
	foreach ($result as $k => $ln):
		updateEstoque( $ln, "fg_compra", "S" );
	endforeach;

	return array( "result" => true );
}
?>