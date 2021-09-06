<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getQueryByFilter( $parameters ) {
	session_start();
	$cadMembroID = $_SESSION['USER']['id_cad_membro'];

	$result = CONN::get()->Execute("
		SELECT ID_UNIDADE, CD_CARGO
		  FROM CAD_ATIVOS 
		 WHERE NR_ANO = YEAR(NOW()) 
		   AND ID_CAD_MEMBRO = ?
	", array($cadMembroID) );
	$unidadeID = $result->fields['ID_UNIDADE'];

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
			if ( $key == "MA" ):
				$where .= " AND DATE_FORMAT(p.DT_NASC,'%m') ".$notStr."IN";
			elseif ( $key == "C" ):
				$where .= " AND cap.TP_ITEM = 'CL' AND cap.ID_TAB_APREND ".$notStr."IN";
			elseif ( $key == "RG" ):
				$where .= " AND p.ID_TAB_TP_REG_ALIM ".$notStr."IN";
      elseif ( $key == "RE" ):
        $where .= " AND p.ID_TAB_TP_REST_ALIM ".$notStr."IN";
			else:
				$where .= " AND";
			endif;

			$prim = true;
			$where .= " (";
			if ( is_array( $parameters["filters"][$key]["vl"] ) ):
				foreach ($parameters["filters"][$key]["vl"] as $value):
					if ( $key == "B" ):
						if ($value == "S"):
							$where .= (!$prim ? " OR " : "") ."p.DT_BAT IS ". ( $value == "S" && !$not ? "NOT NULL" : "NULL");
						elseif ($value == "N"):
							$where .= (!$prim ? " OR " : "") ."p.DT_BAT IS ". ( $value == "N" && !$not ? "NULL" : "NOT NULL");
						elseif (fStrStartWith($value,"A")):
							$where .= (!$prim ? " OR " : "") ."YEAR(p.DT_BAT) ". ( !$not ? " < " : " >= ") . substr($value,1,4);
						else:
							$where .= (!$prim ? " OR " : "") ."YEAR(p.DT_BAT) ". ( !$not ? " = " : " <> ") . $value;
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

	return CONN::get()->Execute("
	  SELECT DISTINCT
			m.ID,
			m.ID_MEMBRO,
			m.ID_CAD_PESSOA,
			p.NM,
			a.DS_CARGO,
      a.DT_NASC,
      a.FONE_CEL,
      a.FONE_RES,
			a.IDADE_HOJE,
			a.IDADE_ANO
		FROM CAD_MEMBRO m
		INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
		INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = p.ID_CAD_PESSOA)
		LEFT JOIN CON_APR_PESSOA cap ON (cap.ID_CAD_PESSOA = p.ID_CAD_PESSOA AND cap.DT_CONCLUSAO IS NULL)
		WHERE a.ID_UNIDADE=$unidadeID $where ORDER BY p.NM"
	,$aWhere);
}

function getMembros( $parameters ) {
	$arr = array();

	$qtdZeros = zeroSizeID();
	$result = getQueryByFilter( $parameters );
  foreach ($result as $k => $f):
    $dtNascimento = strtotime($f['DT_NASC']);

		$aniversario = mktime(0, 0, 0, strftime("%m",$dtNascimento), strftime("%d",$dtNascimento), date("Y") );
		if ( $f['IDADE_ANO'] == $f['IDADE_HOJE'] && strftime("%Y%m",$aniversario) < date("Ym") ):
			$aniversario = mktime(0, 0, 0, strftime("%m",$dtNascimento), strftime("%d",$dtNascimento), date("Y")+1 );
    endif;
    
    $cel = $f['FONE_CEL'];
    $res = $f['FONE_RES'];
    $phone = "";
    if (!is_null($cel)):
      $phone .= $cel;
    endif;
    if (!is_null($res)):
      $phone .= (!is_null($phone) ? " | ". $res : $res);
    endif;

		$arr[] = array(
      "id" => $f['ID'],
			"nm" => $f['NM'],
      "cgo" => $f['DS_CARGO'],
      "pho" => $phone,
			"dm" => $aniversario,
      "ih" => $f['IDADE_HOJE'],
		);
  endforeach;

  return array( "result" => true, "membros" => $arr );
}

function getGraphData() {
	session_start();
	$cadMembroID = $_SESSION['USER']['id_cad_membro'];

	$arr = array();
	$arr["cls"] = array();

	$result = CONN::get()->Execute("
		SELECT ID_UNIDADE, CD_CARGO
		  FROM CAD_ATIVOS 
		 WHERE NR_ANO = YEAR(NOW()) 
		   AND ID_CAD_MEMBRO = ?
	", array($cadMembroID) );
	$unidadeID = $result->fields['ID_UNIDADE'];
	if (empty($unidadeID)):
		return $arr;
	endif;

	$where = "ca.ID_UNIDADE = ?";
	$cargo = $result->fields['CD_CARGO'];
	if (!fStrStartWith($cargo,"2-07")):
		$where .= " OR ca.CD_CARGO LIKE '2-07%'";	
	endif;

	$result = CONN::get()->Execute("
		SELECT a.CD_ITEM_INTERNO, a.CD_COR, a.DS_ITEM, cai.QTD, AVG(a.QTD) AS QT_MD
		FROM (
			SELECT cap.ID_CAD_PESSOA, cap.CD_COR, cap.DS_ITEM, cap.ID_TAB_APREND, cap.CD_ITEM_INTERNO, COUNT(*) AS QTD 
			FROM CON_APR_PESSOA cap
		  INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
			WHERE ($where)
			  AND cap.DT_ASSINATURA IS NOT NULL
			  AND cap.DT_CONCLUSAO IS NULL
			GROUP BY cap.ID_CAD_PESSOA, cap.CD_COR, cap.DS_ITEM, cap.ID_TAB_APREND, cap.CD_ITEM_INTERNO
			) AS a
		INNER JOIN CON_APR_ITEM cai ON (cai.ID = a.ID_TAB_APREND)
		GROUP BY a.CD_ITEM_INTERNO, a.CD_COR, a.DS_ITEM, cai.QTD
		ORDER BY a.CD_ITEM_INTERNO
	",array($unidadeID));
	$series = 0;
	$arr["ticks"] = array();
	foreach ($result as $k => $line):
		$calc = floor( ($line['QT_MD'] / $line['QTD'])*100 );
		$arr["ticks"][] = array( $series, titleCase($line['DS_ITEM'])."<br/>$calc%" );
		$arr["cls"][] = array(
			"color" => $line['CD_COR'],
			"data"	=> array( array( $series, $calc ) )
		);
		$series++;
	endforeach;

	return $arr;
}
?>
