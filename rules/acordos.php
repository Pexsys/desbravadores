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
			if ( $key == "SA" ):
				$where .= " AND fa.TP ".$notStr."IN";
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
		SELECT DISTINCT
			fa.ID,
			fa.CD,
			fa.TP,
			cp.NM AS NM_PATR,
			caa.NM AS NM_BENE
		FROM FIN_ACORDO fa
  INNER JOIN CON_PESSOA cp ON (cp.ID_CAD_PESSOA = fa.ID_CAD_PES_PATR)
   LEFT JOIN FIN_ACO_PESSOA fap ON (fap.ID_FIN_ACORDO = fa.ID)
  INNER JOIN CON_PESSOA caa ON (caa.ID_CAD_PESSOA = fap.ID_CAD_PESSOA)
		WHERE fa.NR_ANO = YEAR(NOW()) $where
	 ORDER BY fa.TP
	";
	return CONN::get()->Execute( $query, $aWhere );
}

function getAcordos( $parameters ) {
	$arr = array();

	$result = getQueryByFilter( $parameters );
	foreach ($result as $k => $f):
		$arr[] = array(
			"id" => $f['ID'],
			"cd" => $f['CD'],
			"pt" => $f['NM_PATR'],
			"bn" => $f['NM_BENE'],
			"tp" => $f['TP']
		);
	endforeach;

	return array( "result" => true, "source" => $arr );
}

function getObjectResult($f){
	return array(
		"id" => $f['ID_CAD_PESSOA'],
		"nm" => $f['NM'],
		"dt" => is_null($f['DT_NASC']) ? "" : date( 'd/m/Y', strtotime($f['DT_NASC']) ),
		"sx" => $f['TP_SEXO'],
		"cp" => fCPF($f['NR_CPF']),
		"fn" => $f['FONE_CEL'],
		"em" => $f['EMAIL']
	);
}

function getPessoas($query,$aWhere){
	$arr = array();

	$result = CONN::get()->Execute($query,$aWhere);
	foreach ($result as $k => $f):
		$arr[] = getObjectResult($f);
	endforeach;
	return array( "result" => true, "source" => $arr );
}

function getPersonByCPF( $parameters ){
	$arr = array();

	return getPessoas("
		SELECT cp.ID_CAD_PESSOA, cp.NM, cp.DT_NASC, cp.NR_CPF, cp.FONE_CEL, cp.EMAIL, cp.TP_SEXO 
		FROM CON_PESSOA cp
		WHERE cp.NR_CPF = ?
	",array($parameters["cpf"]));
}

function getBeneficiados($parameters){
	$arr = array();

	$where = "";
	$aWhere = array(PATTERNS::getBars()->getClubeID());

	if ( isset($parameters["query"]) ):
		$nome = mb_strtoupper($parameters["query"]);
		$where .= "cp.NM LIKE '%$nome%'";
	endif;

	return getPessoas("
			SELECT DISTINCT cp.ID_CAD_PESSOA, cp.NM, cp.DT_NASC, cp.NR_CPF, cp.FONE_CEL, cp.EMAIL, cp.TP_SEXO
			FROM CON_PESSOA cp
		LEFT JOIN CAD_MEMBRO cm ON (cm.ID_CAD_PESSOA = cp.ID_CAD_PESSOA AND cm.ID_CLUBE = ?)
		LEFT JOIN CAD_ATIVOS ca ON (ca.ID_CAD_MEMBRO = cm.ID)
			WHERE $where
		ORDER BY ca.NR_ANO, cp.NM
		",$aWhere);
}

function getPatrocinadores($parameters){
	$arr = array();

	$where = "(cp.DT_NASC IS NULL OR cp.IDADE_HOJE > ?)";
	$aWhere = array(PATTERNS::getBars()->getClubeID(), 15);

	if ( isset($parameters["query"]) ):
		$nome = mb_strtoupper($parameters["query"]);
		$where .= " AND cp.NM LIKE '%$nome%'";
	endif;

	return getPessoas("
			SELECT DISTINCT cp.ID_CAD_PESSOA, cp.NM, cp.DT_NASC, cp.NR_CPF, cp.FONE_CEL, cp.EMAIL, cp.TP_SEXO
			FROM CON_PESSOA cp
		LEFT JOIN CAD_MEMBRO cm ON (cm.ID_CAD_PESSOA = cp.ID_CAD_PESSOA AND cm.ID_CLUBE = ?)
		LEFT JOIN CAD_ATIVOS ca ON (ca.ID_CAD_MEMBRO = cm.ID)
			WHERE $where
		ORDER BY ca.NR_ANO, cp.NM
		",$aWhere);
}


?>
