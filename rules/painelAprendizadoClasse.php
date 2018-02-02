<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getGraphData() {
	session_start();
	$cadMembroID = $_SESSION['USER']['id_cad_membro'];

	$arr = array();
	

	$arr["cls"] = array();

	$like = "";
	$result = CONN::get()->Execute("
		SELECT CD_CARGO, CD_CARGO2
		  FROM CON_ATIVOS
		 WHERE ID_CAD_MEMBRO = ?
	", array($cadMembroID) );
	$cargo = $result->fields['CD_CARGO'];
	if (fStrStartWith($cargo,"2-07")):
		$cargo = $result->fields['CD_CARGO2'];
	endif;
	if (empty($cargo)):
		return $arr;
	endif;
	if ($cargo != "2-04-00" && fStrStartWith($cargo,"2-04")):
		$like = "01-".substr($cargo,-2);
	endif;
	$result = CONN::get()->Execute("
		SELECT a.CD_ITEM_INTERNO, a.CD_COR, a.DS_ITEM, cai.QTD, AVG(a.QTD) AS QT_MD
		FROM (
			SELECT cap.ID_CAD_PESSOA, cap.CD_COR, cap.DS_ITEM, cap.ID_TAB_APREND, cap.CD_ITEM_INTERNO, COUNT(*) AS QTD 
			FROM CON_APR_PESSOA cap
		  INNER JOIN CON_ATIVOS at ON (at.ID_CAD_PESSOA = cap.ID_CAD_PESSOA)
			WHERE cap.CD_ITEM_INTERNO LIKE '$like%'
			  AND cap.DT_ASSINATURA IS NOT NULL
			  AND cap.DT_CONCLUSAO IS NULL
			GROUP BY cap.ID_CAD_PESSOA, cap.CD_COR, cap.DS_ITEM, cap.ID_TAB_APREND, cap.CD_ITEM_INTERNO
			) AS a
		INNER JOIN CON_APR_ITEM cai ON (cai.ID = a.ID_TAB_APREND)
		GROUP BY a.CD_ITEM_INTERNO, a.CD_COR, a.DS_ITEM, cai.QTD
		ORDER BY a.CD_ITEM_INTERNO
	");
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