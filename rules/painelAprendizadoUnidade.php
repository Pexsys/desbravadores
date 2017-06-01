<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getGraphData() {
	session_start();
	$membroID = $_SESSION['USER']['id_cad_pessoa'];

	$arr = array();
	fConnDB();

	$arr["cls"] = array();

	$result = $GLOBALS['conn']->Execute("
		SELECT ID_UNIDADE, CD_CARGO
		  FROM CAD_ATIVOS 
		 WHERE NR_ANO = YEAR(NOW()) 
		   AND ID = ?
	", array($membroID) );
	$unidadeID = $result->fields['ID_UNIDADE'];
	if (empty($unidadeID)):
		return $arr;
	endif;

	$where = "ca.ID_UNIDADE = ?";
	$cargo = $result->fields['CD_CARGO'];
	if (!fStrStartWith($cargo,"2-07")):
		$where .= " OR ca.CD_CARGO LIKE '2-07%'";	
	endif;

	$result = $GLOBALS['conn']->Execute("
		SELECT a.CD_ITEM_INTERNO, a.CD_COR, a.DS_ITEM, cai.QTD, AVG(a.QTD) AS QT_MD
		FROM (
			SELECT cap.ID_CAD_PESSOA, cap.CD_COR, cap.DS_ITEM, cap.ID_TAB_APREND, cap.CD_ITEM_INTERNO, COUNT(*) AS QTD 
			FROM CON_APR_PESSOA cap
		  INNER JOIN CON_ATIVOS ca ON (ca.ID = cap.ID_CAD_PESSOA)
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