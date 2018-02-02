<?php
@require_once("../include/functions.php");
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
			if ( $key == "HT" ):
				$where .= " AND tm.TP ".$notStr."IN";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $parameters["filters"][$key]["vl"] ) ):
				foreach ($parameters["filters"][$key]["vl"] as $value):
					if ( empty($value) ):
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
			tm.ID,
			tm.TP,
			tm.DS,
			tm.QT_EST,
			tm.FUNDO,
			ta.CD_ITEM_INTERNO,
			ta.DS_ITEM
		FROM TAB_MATERIAIS tm
   LEFT JOIN TAB_APRENDIZADO ta ON (ta.ID = tm.ID_TAB_APREND AND ta.TP_ITEM = 'ES')
		WHERE QT_EST > 0 $where
	 ORDER BY tm.TP, tm.DS
	";
	//echo $query;
	return CONN::get()->Execute( $query, $aWhere );
}

function getEstoque( $parameters ) {
	$arr = array();
	
	
	$result = getQueryByFilter($parameters);
	if (!is_null($result)):
		foreach ($result as $k => $fields):
		    $ds = $fields['DS'];
		    $ds .= (!is_null($fields['DS_ITEM'])? " - ".$fields['CD_ITEM_INTERNO']."-".$fields['DS_ITEM'] : "");
    		if ( !empty($fields['FUNDO']) ):
    			$ds .= " - FUNDO ". ($fields['FUNDO'] == "BR" ?  "BRANCO" : "CAQUI");
    		endif;
    		
			$arr[] = array( 
				"id" => $fields['ID'],
				"tp" => ($fields['TP']),
				"ds" => ($ds),
				"qt" => $fields['QT_EST']
			);
		endforeach;
	endif;
	
	return array( "result" => true, "est" => $arr );
}

function setEstoque( $parameters ){
	$frm = $parameters["frm"];
	$qtItens = max( $frm["qt_itens"], 0 );
	
	
	$materiais = new MATERIAIS();
	
	if ($parameters["tp"] == "edit"):
		$materiais->setQtdEstoque( $frm["id"], $qtItens );
	else:
		$materiais->addItemEstoque( $frm["id"], $qtItens );
	endif;
	
	return array( "result" => true );
}

function getItem( $parameters ){
	$arr = array();
	$id = $parameters["id"];
	$arr["id"] = $id;

	
	$result = CONN::get()->Execute("
		SELECT TP, QT_EST
		FROM TAB_MATERIAIS
		WHERE ID = ?
	", array($id) );
	if (!$result->EOF):
		$arr["tp"] = $result->fields["TP"];
		$arr["qt_est"] = $result->fields["QT_EST"];
	endif;
	
	return $arr;
}
?>