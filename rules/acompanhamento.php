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
	$cadMembroID = $_SESSION['USER']['id_cad_membro'];

	$where = "";
	$result = $GLOBALS['conn']->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID_CAD_MEMBRO = ?
	", array($membroID) );
	$cargo = $result->fields['CD_CARGO'];
	if (fStrStartWith($cargo,"2-07")):
		$cargo = $result->fields['CD_CARGO2'];
	endif;
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$like = "01-".substr($cargo,-2);
		$where .= " AND cai.CD_ITEM_INTERNO LIKE '$like%' AND ca.CD_CARGO NOT LIKE '2-%'";
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
				$where .= " AND ca.TP_SEXO ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND ca.ID_UNIDADE ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND cai.TP_ITEM = ? AND ah.ID_TAB_APREND ".$notStr."IN";
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
							$where .= (!$prim ? " OR " : "") ."ca.CD_FANFARRA IS ".( !$not ? "NOT NULL" : "NULL");
						else:
							$where .= (!$prim ? " OR " : "") ."ca.CD_CARGO ". ( empty($value) ? "IS ".$notStr."NULL" : $notStr."LIKE '$value%'");
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
			ca.NM,
			ah.ID_CAD_PESSOA,
			ah.ID_TAB_APREND,
			cai.TP_ITEM,
			cai.CD_ITEM_INTERNO,			
			cai.DS_ITEM,
			ah.DT_INICIO,
			ah.DT_CONCLUSAO,
			ah.DT_AVALIACAO,
			cai.QTD AS QT_TOTAL,
			(SELECT COUNT(*) 
               FROM CON_APR_PESSOA 
              WHERE ID_TAB_APREND = ah.ID_TAB_APREND 
                AND ID_CAD_PESSOA = ah.ID_CAD_PESSOA
                AND DT_ASSINATURA IS NOT NULL) AS QTD
		FROM APR_HISTORICO ah
		INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
		INNER JOIN CON_APR_ITEM cai ON (cai.ID = ah.ID_TAB_APREND)
		WHERE ah.DT_INVESTIDURA IS NULL 
		  AND cai.TP_ITEM = 'CL' $where
		ORDER BY ca.NM, ah.DT_INICIO, cai.CD_ITEM_INTERNO
	";

	return $GLOBALS['conn']->Execute( $query, $aWhere );
}

function getData( $parameters ) {
	$arr = array();
	
	fConnDB();
	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $fields):

		$perc = floor(($fields['QTD'] / max(1,$fields['QT_TOTAL']))*100);		
		
		if ($perc < 51):
			$cl = 'danger';
		elseif ($perc < 85):
			$cl = 'warning';
		elseif ($perc == 100):
			$cl = 'success';
		endif;

		$arr[] = array( 
			"ip" => $fields['ID_CAD_PESSOA'],
			"ia" => $fields['ID_TAB_APREND'],
			"di" => is_null($fields['DT_INICIO']) ? "" : strftime("%d/%m/%Y",strtotime($fields['DT_INICIO'])),
			"dc" => is_null($fields['DT_CONCLUSAO']) ? "" : strftime("%d/%m/%Y",strtotime($fields['DT_CONCLUSAO'])),
			"da" => is_null($fields['DT_AVALIACAO']) ? "" : strftime("%d/%m/%Y",strtotime($fields['DT_AVALIACAO'])),
			"nm" => $fields['NM'],
			"tp" => $fields['DS_ITEM'],
			"pg" => array( "pc" => $perc, "cl" => $cl)
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
			
			$rs = $GLOBALS['conn']->Execute( "SELECT ID_TAB_APREND FROM TAB_APR_ITEM WHERE ID = ?", $reqID );
			if (!$rs->EOF):
				$uh = updateHistorico(
					$ip, 
					$rs->fields["ID_TAB_APREND"],
					array(
						"dt_inicio" => "N",
						"dt_conclusao" => "N",
						"dt_avaliacao" => "N",
						"dt_investidura" => "N"
					), 
					null
				);
				marcaRequisitoID( getDateNull($v), $uh["id"], $reqID );
			endif;
		endif;
	endforeach;
	
	analiseHistoricoPessoa($ip);
	
	return array( "result" => true );
}
?>