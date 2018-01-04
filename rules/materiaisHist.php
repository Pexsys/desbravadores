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
			elseif ( $key == "HT" ):
				$where .= " AND cmh.TP ".$notStr."IN";
			elseif ( $key == "U" ):
				$where .= " AND ca.ID_UNIDADE ".$notStr."IN";
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
			SELECT
				cmh.ID,
				ca.NM,
				cmh.TP,
				cmh.DS,
				cmh.DT_ENTREGA,
				cmh.COMPL
			FROM CON_MAT_HISTORICO cmh
			INNER JOIN CON_ATIVOS ca ON (ca.ID = cmh.ID_CAD_PESSOA)
			WHERE 1=1 $where
		 ORDER BY ca.NM, cmh.TP
		";
		//echo $query;
		return $GLOBALS['conn']->Execute( $query, $aWhere );
	endif;
	return null;
}

function getHistorico( $parameters ) {
	$arr = array();
	
	fConnDB();
	$qtdZeros = zeroSizeID();

	$result = getQueryByFilter($parameters);
	if (!is_null($result)):
		foreach ($result as $k => $fields):
			$arr[] = array( 
				"id" => $fields['ID'],
				"tp" => $fields['TP'],
				"ds" => $fields['DS']. ( !is_null($fields['COMPL']) ? " [".$fields['COMPL']."]" : ""),
				"nm" => $fields['NM'],
				"dt" => strtotime($fields['DT_ENTREGA'])
			);
		endforeach;
	endif;
	
	return array( "result" => true, "hist" => $arr );
}
?>