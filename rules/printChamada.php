<?php
@require_once("../include/functions.php");
@require_once("../include/domains.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getDomains(){
	
	
	$ma = array();
	$result = CONN::get()->Execute("
		SELECT DISTINCT DATE_FORMAT(ce.DTHORA_EVENTO_INI,'%Y-%m') AS ANO_MES
		  FROM CAD_EVENTOS ce
	INNER JOIN RGR_CHAMADA rc ON (rc.ID_EVENTO = ce.ID_EVENTO)
		 WHERE ce.DTHORA_EVENTO_INI >= NOW()
		   AND rc.ID_TAB_RGR_CHAMADA IS NOT NULL
	  ORDER BY 1
	");
	foreach ($result as $k => $l):
		$desc = mb_strtoupper(fDescMes(substr($l["ANO_MES"],5,2)));
		$ma[] = array( "id" => $l["ANO_MES"], "ds" => $desc, "sb" => substr($l["ANO_MES"],0,4) );
	endforeach;
	
	$un = getDomainUnidades(true);

	return array( "result" => true, "meses" => $ma, "unidade" => $un );
}
?>