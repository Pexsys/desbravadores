<?php
@require_once("../include/functions.php");
@require_once("../include/acompanhamento.php");
@require_once("../include/historico.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	session_start();
	$membroID = $_SESSION['USER']['id_cad_pessoa'];

	$where = "";
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID = ?
	", array($membroID) );
	$cargo = $result->fields['CD_CARGO'];
	if (fStrStartWith($cargo,"2-07")):
		$cargo = $result->fields['CD_CARGO2'];
	endif;
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$like = "01-".substr($cargo,-2);
		$where .= " AND cap.CD_ITEM_INTERNO LIKE '$like%'";
	endif;	
	
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
			elseif ( $key == "C" ):
				$where .= " AND cap.TP_ITEM = ? AND cap.ID_TAB_APREND ".$notStr."IN";
				$aWhere[] = "CL";
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
					elseif ( $key == "HA" ):
						if ( $value == "0" ):
							$where .= (!$prim ? " OR " : "") ."cap.DT_CONCLUSAO IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "1" ):
							$where .= (!$prim ? " OR " : "") ."cap.DT_CONCLUSAO IS NOT NULL AND cap.DT_AVALIACAO IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "2" ):
							$where .= (!$prim ? " OR " : "") ."YEAR(cap.DT_AVALIACAO) ".( !$not ? " = " : " <> ")." YEAR(NOW())";
						elseif ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."cap.DT_CONCLUSAO IS NOT NULL AND cap.DT_AVALIACAO IS NOT NULL AND cap.DT_INVESTIDURA IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "4" ):
							$where .= (!$prim ? " OR " : "") ."YEAR(cap.DT_INVESTIDURA) ".( !$not ? " = " : " <> ")." YEAR(NOW())";
						elseif ( $value == "5" ):
							$where .= (!$prim ? " OR " : "") ."YEAR(cap.DT_AVALIACAO) ".( !$not ? " < " : " <> ")." YEAR(NOW())";
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
		endforeach;
	endif;	

//echo $where;
//exit;

	$query = "
		SELECT 
			at.NM,
			cap.ID_CAD_PESSOA,
			cap.ID_TAB_APREND,
			cap.CD_ITEM_INTERNO,			
			cap.DS_ITEM,
			cap.DT_INICIO,
			cap.DT_CONCLUSAO,
			cap.DT_AVALIACAO,
			cai.QTD AS QT_TOTAL,
			COUNT(*) AS QTD
		FROM CON_APR_PESSOA cap
		INNER JOIN CON_APR_ITEM cai ON (cai.ID = cap.ID_TAB_APREND)
		INNER JOIN CON_ATIVOS at ON (at.ID = cap.ID_CAD_PESSOA)
		WHERE cap.DT_ASSINATURA IS NOT NULL $where
	 GROUP BY at.NM, cap.ID_CAD_PESSOA, cap.TP_ITEM, cap.CD_ITEM_INTERNO, cap.DS_ITEM, cap.DT_INICIO, cap.DT_CONCLUSAO, cap.DT_AVALIACAO, cai.QTD
	 ORDER BY at.NM, cap.DT_INICIO, cap.CD_ITEM_INTERNO
	";

	return $GLOBALS['conn']->Execute( $query, $aWhere );
}

function getData( $parameters ) {
	$arr = array();
	
	fConnDB();
	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):
		$arr[] = array( 
			"ip" => utf8_encode($fields['ID_CAD_PESSOA']),
			"ia" => utf8_encode($fields['ID_TAB_APREND']),
			"di" => is_null($fields['DT_INICIO']) ? "" : strftime("%d/%m/%Y",strtotime($fields['DT_INICIO'])),
			"dc" => is_null($fields['DT_CONCLUSAO']) ? "" : strftime("%d/%m/%Y",strtotime($fields['DT_CONCLUSAO'])),
			"da" => is_null($fields['DT_AVALIACAO']) ? "" : strftime("%d/%m/%Y",strtotime($fields['DT_AVALIACAO'])),
			"nm" => utf8_encode($fields['NM']),
			"tp" => utf8_encode($fields['DS_ITEM']),
			"pg" => floor(($fields['QTD'] / $fields['QT_TOTAL'])*100)
		);
	endforeach;
	
	return array( "result" => true, "acomp" => $arr );
}

function setRequisito( $parameters ) {
	$frm = $parameters["frm"];
	
	$bc = $frm["cd_bar"];
	$ip = $frm["id_cad_pessoa"];
	
	fConnDB();
	
	foreach ($frm as $f => $v):
		if (fStrStartWith($f,"dt-req-")):
			$reqID = substr($f, 7);
			
			$rs = $GLOBALS['conn']->Execute( "SELECT ID_TAB_ITEM FROM TAB_APR_ITEM WHERE ID = ?", $reqID );
			if (!$rs->EOF):
				$uh = updateHistorico(
					$ip, 
					$rs->fields["ID_TAB_ITEM"],
					array(
						"dt_inicio" => "N",
						"dt_conclusao" => "N",
						"dt_avaliacao" => "N",
						"dt_investidura" => "N"
					), 
					null
				);
				marcaRequisitoID( getDateNull($v), $ip, $uh["id"], $reqID );
			endif;
		endif;
	endforeach;
	
	analiseHistoricoPessoa($ip);
	
	return array( "result" => true );
}
?>