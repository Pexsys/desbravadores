<?php
@require_once("../include/functions.php");
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
			elseif ( $key == "T" ):
				$where .= " AND ca.ID_CAD_MEMBRO ".$notStr."IN";
			elseif ( $key == "I" ):
				$where .= " AND cm.ID ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND ca.ID_UNIDADE ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND ap.TP_ITEM = 'CL' AND ap.ID ".$notStr."IN";
			elseif ( $key == "E" ):
				$where .= " AND ap.TP_ITEM = 'ES' AND ap.CD_AREA_INTERNO <> 'ME' AND ap.ID ".$notStr."IN";
			elseif ( $key == "M" ):
				$where .= " AND ap.TP_ITEM = 'ES' AND ap.CD_AREA_INTERNO = 'ME' AND ap.ID ".$notStr."IN";
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
					elseif ( $key == "HH" ):
						if ( $value == "0" ):
							$where .= (!$prim ? " OR " : "") ."ah.DT_CONCLUSAO IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "1" ):
							$where .= (!$prim ? " OR " : "") ."ah.DT_AVALIACAO IS ".( !$not ? "NULL" : "NOT NULL");
						elseif ( $value == "2" ):
							$where .= (!$prim ? " OR " : "") ."YEAR(ah.DT_AVALIACAO) ".( !$not ? " = " : " <> ")." YEAR(NOW())";
						elseif ( $value == "3" ):
							$where .= (!$prim ? " OR " : "") ."ah.DT_INVESTIDURA IS ".( !$not ? "NULL" : "NOT NULL");
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

	if (!empty($where)):
		$query = "
			SELECT DISTINCT
				ah.ID,
				cp.NM,
				ap.TP_ITEM,
				ta.DS,
				ap.DS_ITEM,
				ap.CD_ITEM_INTERNO,
				ah.DT_AVALIACAO,
				tm.NR_PG_ASS
			FROM CAD_PESSOA cp
			INNER JOIN CAD_MEMBRO cm ON (cm.ID_CAD_PESSOA = cp.ID)
			LEFT JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cp.ID)
			LEFT JOIN APR_HISTORICO ah ON (ah.id_cad_pessoa = cp.ID AND ah.DT_INVESTIDURA IS NOT NULL)
			LEFT JOIN TAB_APRENDIZADO ap ON (ap.id = ah.id_tab_aprend)
			LEFT JOIN TAB_MATERIAIS tm ON (tm.id_tab_aprend = ah.id_tab_aprend)
			INNER JOIN TAB_TP_APRENDIZADO ta ON (ta.id = ap.tp_item)
			WHERE 1=1 $where
		 ORDER BY ca.NM, ap.CD_ITEM_INTERNO
		";
		//echo $query;
		//print_r($aWhere);
		return $GLOBALS['conn']->Execute( $query, $aWhere );
	endif;
	return null;
}

function getAprHist( $parameters ) {
	$arr = array();

	$qtdZeros = zeroSizeID();

	$result = getQueryByFilter($parameters);
	if (!is_null($result)):
		foreach ($result as $k => $fields):
			$ds = ($fields['DS_ITEM']) . ($fields['TP_ITEM'] == "ES" ? " - ".$fields['CD_ITEM_INTERNO'] : "");
			$arr[] = array(
				"id" => $fields['ID'],
				"nm" => $fields['NM'],
				"dstpi" => $fields['DS'],
				"dsitm" => $ds,
				"dta" => (empty($fields['DT_AVALIACAO']) ? "" : strtotime($fields['DT_AVALIACAO']) ),
				"pg" =>  ($fields['TP_ITEM'] == "ES" ? $fields['NR_PG_ASS'] : "")
			);
		endforeach;
	endif;

	return array( "result" => true, "aprhist" => $arr );
}
?>
