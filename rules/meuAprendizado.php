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

	$dtInicio = null;

	//OBJETIVO DO CLUBE
	$result = $GLOBALS['conn']->Execute("
		SELECT DISTINCT DTHORA_EVENTO_INI
		  FROM CAD_EVENTOS 
		 WHERE FLAG_PUBLICACAO = 'S'
		   AND FG_INSTRUCAO = 'S'
		   AND YEAR(DTHORA_EVENTO_INI) = YEAR(NOW())
	  ORDER BY DTHORA_EVENTO_INI
	");
	if (!$result->EOF):
		$qtTotal = $result->RecordCount();
		
		$arr["ob"] = array( 
			"label"		=> "&nbsp;Cronograma do Clube", 
			"data"		=> array(), 
			"color"		=> "#F5F5F5", 
			"lines"		=> array( "show" => true ),
			"dashes"	=> array( "show" => true, "lineWidth" => 1, "dashLength" => array( 10, 30 ) ),
			"checkbox"	=> false
		);

		$dtAgora = date("Y-m-d");
		$dtInicio = $result->fields["DTHORA_EVENTO_INI"];
		$pct = 0;
		$pctAnt = 0;
		$qtDt = 0;
		$high = false;
		foreach($result as $k => $line):
			$dateLine = strtotime($line["DTHORA_EVENTO_INI"]);
			
			$pctAnt = $pct;
			$pct = floor((100/($qtTotal-1))*$qtDt++);
			if (!$high):
				$d = date("Y-m-d",$dateLine);
				if ($d == $dtAgora):
					$arr["ob"]["idx"] = $qtDt-1;
					$high = true;
				elseif ($d > $dtAgora):
					$arr["ob"]["data"][] = array( strtotime($dtAgora)."000", round(($pct+$pctAnt)/2,0) );
					$arr["ob"]["idx"] = $qtDt-1;
					$high = true;
				endif;
			endif;
			
			
			$arr["ob"]["data"][] = array( strtotime($line["DTHORA_EVENTO_INI"])."000", $pct );
		endforeach;
	endif;

	//MINHAS CLASSES ABERTAS NO ANO
	$rsCls = $GLOBALS['conn']->Execute("
		SELECT DISTINCT ID_TAB_APREND, DS_ITEM, CD_COR, CD_ITEM_INTERNO
		  FROM CON_APR_PESSOA 
		 WHERE ID_CAD_PESSOA = ?
		   AND YEAR(DT_INICIO) = YEAR(NOW())
		   AND DT_CONCLUSAO IS NULL
	  ORDER BY CD_ITEM_INTERNO
	", array($membroID) );
	if (!$rsCls->EOF):
		foreach($rsCls as $j => $lnc):
			$result = $GLOBALS['conn']->Execute("
				SELECT X.DT_ASSINATURA, Y.QTD, COUNT(*) AS QT
				  FROM CON_APR_PESSOA X
				INNER JOIN CON_APR_ITEM Y ON (Y.ID = X.ID_TAB_APREND)
				 WHERE X.ID_CAD_PESSOA = ?
				   AND X.DT_ASSINATURA IS NOT NULL
				   AND X.ID_TAB_APREND = ?
				   AND X.DT_CONCLUSAO IS NULL
				   AND YEAR(X.DT_INICIO) = YEAR(NOW())
				  GROUP BY X.DT_ASSINATURA, Y.QTD
				  ORDER BY X.DT_ASSINATURA
			", array($membroID, $lnc["ID_TAB_APREND"]) );
			if (!$result->EOF):
				$qtDt = 0;
				
				$iA = "apr".$lnc["ID_TAB_APREND"];

				$arr[$iA] = array( 
					"label"		=> "&nbsp;".titleCase($lnc["DS_ITEM"]), 
					"data"		=> array(), 
					"color"		=> $lnc["CD_COR"], 
					"lines"		=> array( "show" => true, "fill" => true ),
					"checkbox"	=> true
				);
				$arr[$iA]["data"][] = array(strtotime($dtInicio)."000", $qtDt );

				foreach($result as $k => $line):
					$qtDt += $line["QT"];
					$arr[$iA]["data"][] = array( 
						strtotime($line["DT_ASSINATURA"])."000",
						floor((100/$line["QTD"])*$qtDt)
					);
				endforeach;
			endif;
		endforeach;
	endif;
	return $arr;
}
?>