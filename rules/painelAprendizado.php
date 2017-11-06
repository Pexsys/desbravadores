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
	
	$arr["clsP"] = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT a.CD_ITEM_INTERNO, a.CD_COR, a.DS_ITEM, cai.QTD, AVG(a.QTD) AS QT_MD
		FROM (
			SELECT cap.ID_CAD_PESSOA, cap.CD_COR, cap.DS_ITEM, cap.ID_TAB_APREND, cap.CD_ITEM_INTERNO, COUNT(*) AS QTD 
			  FROM CON_APR_PESSOA cap
	    INNER JOIN CON_ATIVOS ca ON (ca.ID = cap.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
			 WHERE cap.CD_ITEM_INTERNO LIKE '01%'
			   AND cap.DT_ASSINATURA IS NOT NULL
			   AND cap.DT_CONCLUSAO IS NULL
		  GROUP BY cap.ID_CAD_PESSOA, cap.CD_COR, cap.DS_ITEM, cap.ID_TAB_APREND, cap.CD_ITEM_INTERNO
			) AS a
		INNER JOIN CON_APR_ITEM cai ON (cai.ID = a.ID_TAB_APREND)
		GROUP BY a.CD_ITEM_INTERNO, a.CD_COR, a.DS_ITEM, cai.QTD
		ORDER BY a.CD_ITEM_INTERNO
	");
	$series = 0;
	foreach ($result as $k => $line):
		$pctCalc = floor( ($line['QT_MD'] / $line['QTD'])*100 );
		$arr["clsP"][] = array(
			"label" => $pctCalc,
			"color" => $line['CD_COR'],
			"data"	=> array( array( $series, $pctCalc ) ),
			"bars"	=> array( "order" => floor($series/2) )
		);
		$series++;
	endforeach;

	//ANDAMENDO MÉDIO DAS CLASSES REGULARES
	$arr["rgP"] = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT AVG(b.PCT) AS MED
		FROM (
			SELECT a.ID_TAB_APREND, ((AVG(a.QTD) / cai.QTD)*100) AS PCT
			FROM (
				SELECT cap.ID_CAD_PESSOA, cap.ID_TAB_APREND, COUNT(*) AS QTD 
				  FROM CON_APR_PESSOA cap
	        INNER JOIN CON_ATIVOS ca ON (ca.ID = cap.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE cap.CD_ITEM_INTERNO LIKE '01%00'
			       AND cap.DT_ASSINATURA IS NOT NULL
				   AND cap.DT_CONCLUSAO IS NULL
			  GROUP BY cap.ID_CAD_PESSOA, cap.ID_TAB_APREND
				) AS a
			INNER JOIN CON_APR_ITEM cai ON (cai.ID = a.ID_TAB_APREND)
			GROUP BY a.ID_TAB_APREND
			ORDER BY a.ID_TAB_APREND
		) AS b
	");
	$pct = 0;
	if (!$result->EOF):
		$pct = $result->fields['MED'];
		$arr["rgP"][] = array( 
			"label"		=> "Completado", 
			"data"		=> array(0,floor($pct)),
			"color"		=> "#00FF00"
		);
	endif;
	$arr["rgP"][] = array( 
		"label"		=> "Pendente", 
		"data"		=> array(1,floor(100-$pct)),
		"color"		=> "#FF0000"
	);
	
	//ANDAMENDO MÉDIO DAS CLASSES AVANÇADAS
	$arr["avP"] = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT AVG(b.PCT) AS MED
		FROM (
			SELECT a.ID_TAB_APREND, ((AVG(a.QTD) / cai.QTD)*100) AS PCT
			FROM (
				SELECT cap.ID_CAD_PESSOA, cap.ID_TAB_APREND, COUNT(*) AS QTD 
				  FROM CON_APR_PESSOA cap
	        INNER JOIN CON_ATIVOS ca ON (ca.ID = cap.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE cap.CD_ITEM_INTERNO LIKE '01%01'
			       AND cap.DT_ASSINATURA IS NOT NULL
				   AND cap.DT_CONCLUSAO IS NULL
			  GROUP BY cap.ID_CAD_PESSOA, cap.ID_TAB_APREND
				) AS a
			INNER JOIN CON_APR_ITEM cai ON (cai.ID = a.ID_TAB_APREND)
			GROUP BY a.ID_TAB_APREND
			ORDER BY a.ID_TAB_APREND
		) AS b
	");
	$pct = 0;
	if (!$result->EOF):
		$pct = $result->fields['MED'];
		$arr["avP"][] = array( 
			"label"		=> "Completado", 
			"data"		=> array(0,floor($pct)),
			"color"		=> "#00FF00"
		);
	endif;
	$arr["avP"][] = array( 
		"label"		=> "Pendente", 
		"data"		=> array(1,floor(100-$pct)),
		"color"		=> "#FF0000"
	);
	
	$qtdRegulares = 0;
	$result = $GLOBALS['conn']->Execute("
		SELECT COUNT(*) AS QT
		  FROM TAB_APRENDIZADO 
		 WHERE CD_ITEM_INTERNO LIKE '01%00'
		   AND TP_ITEM = ?
	", array("CL") );
	if (!$result->EOF):
		$qtdRegulares = $result->fields['QT'];
	endif;

	$qtdAvancadas = 0;
	$result = $GLOBALS['conn']->Execute("
		SELECT COUNT(*) AS QT
		  FROM TAB_APRENDIZADO 
		 WHERE CD_ITEM_INTERNO LIKE '01%01'
		   AND TP_ITEM = ?
	", array("CL") );
	if (!$result->EOF):
		$qtdAvancadas = $result->fields['QT'];
	endif;

	//ANALISE GRAFICA DAS CLASSES COMPLETADAS
	$arr["clsC"] = array();
	$result = $GLOBALS['conn']->Execute("
		SELECT a.TP_ITEM, a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO, a.DS_ITEM, a.CD_COR, COUNT(*) AS QTD
		FROM APR_HISTORICO h
		INNER JOIN TAB_APRENDIZADO a ON (a.ID = h.ID_TAB_APREND)
		WHERE a.TP_ITEM = ?
		AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
		GROUP BY a.TP_ITEM, a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO, a.DS_ITEM, a.CD_COR
		ORDER BY a.CD_ITEM_INTERNO
	", array("CL") );
	$series = 0;
	foreach ($result as $k => $line):
		$arr["clsC"][] = array(
			"label" => $line['QTD'],
			"color" => $line['CD_COR'],
			"data"	=> array( array( $series, $line['QTD'] ) ),
			"bars"	=> array( "order" => floor($series/2) )
		);
		$series++;
	endforeach;

	//CLASSES REGULARES A COMPLETAR
	$aCompletar = 0;
	$result = $GLOBALS['conn']->Execute("
		SELECT COUNT(*) AS QT_RG
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%00'
				   AND YEAR(ah.DT_AVALIACAO) < YEAR(NOW())
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID)
		WHERE (a.QT IS NULL OR a.QT < ?) 
		  AND at.FG_REU_SEM = 'S'
	", $qtdRegulares );
	if (!$result->EOF):
		$aCompletar = $result->fields['QT_RG'];
	endif;

	//CLASSES REGULARES COMPLETADAS
	$aCompletadas = 0;
	$result = $GLOBALS['conn']->Execute("
		SELECT COUNT(*) AS QT_RG_OK
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%00'
				   AND (YEAR(ah.DT_CONCLUSAO) = YEAR(NOW()) OR YEAR(ah.DT_INVESTIDURA) = YEAR(NOW()))
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID)
		WHERE a.QT < ?
		  AND at.FG_REU_SEM = 'S'
	", $qtdRegulares );
	$arr["rgC"] = array();
	$pct = 0;
	if (!$result->EOF):
		$aCompletadas = $result->fields['QT_RG_OK'];
		$pct = floor(($aCompletadas/max($aCompletar,1))*100);
		$arr["rgC"][] = array( 
			"label"		=> "Completadas ($aCompletadas)", 
			"data"		=> array(0,$pct),
			"color"		=> "#00FF00"
		);
	endif;
	$arr["rgC"][] = array( 
		"label"		=> "Pendentes (". ($aCompletar-$aCompletadas) .")", 
		"data"		=> array(1,floor(100-$pct)),
		"color"		=> "#FF0000"
	);

	//CLASSES AVANCADAS A COMPLETAR
	$aCompletar = 0;
	$result = $GLOBALS['conn']->Execute("
		SELECT COUNT(*) AS QT_AV
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%01'
				   AND YEAR(ah.DT_AVALIACAO) < YEAR(NOW())
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID)
		WHERE (a.QT IS NULL OR a.QT < ?)
		  AND at.FG_REU_SEM = 'S'
	", $qtdRegulares );
	if (!$result->EOF):
		$aCompletar = $result->fields['QT_AV'];
	endif;

	//CLASSES AVANCADAS COMPLETADAS
	$aCompletadas = 0;
	$result = $GLOBALS['conn']->Execute("
		SELECT COUNT(*) AS QT_AV_OK
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%01'
				   AND (YEAR(ah.DT_CONCLUSAO) = YEAR(NOW()) OR YEAR(ah.DT_INVESTIDURA) = YEAR(NOW()))
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID)
		WHERE a.QT < ?
		  AND at.FG_REU_SEM = 'S'
	", $qtdRegulares );
	$arr["avC"] = array();
	$pct = 0;
	if (!$result->EOF):
		$aCompletadas = $result->fields['QT_AV_OK'];
		$pct = floor(($aCompletadas/max($aCompletar,1))*100);
		$arr["avC"][] = array( 
			"label"		=> "Completadas ($aCompletadas)", 
			"data"		=> array(0,$pct),
			"color"		=> "#00FF00"
		);
	endif;
	$arr["avC"][] = array( 
		"label"		=> "Pendentes (". ($aCompletar-$aCompletadas) .")", 
		"data"		=> array(1,floor(100-$pct)),
		"color"		=> "#FF0000"
	);

	return $arr;
}

function getClasses( $parameters ) {
	$arr = array();
	fConnDB();
	
	$id = $parameters["id"];
	
	$where = "";
	if (!empty($parameters["iil"])):
		$where .= " AND ta.CD_ITEM_INTERNO LIKE '".$parameters["iil"]."%'";
	endif;

	$str = "";
	$str .= "<div class=\"col-lg-12\">";
	
	//CLASSES EM ANDAMENTO
	$rs = $GLOBALS['conn']->Execute("
		  SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		   WHERE ta.ID_CAD_PESSOA = ? AND ta.TP_ITEM = ? AND ta.DT_CONCLUSAO IS NULL $where
		GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
		ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
	", array( $id, "CL" ) );
	if (!$rs->EOF):
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-warning\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><b>Classes em Andamento</b></div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
				
		foreach ($rs as $ks2 => $det):
			$qtdReq =  $rs->fields["QT_REQ"];
			$tabAprID =  $rs->fields["ID_TAB_APREND"];
			$pct = 0;
			$qtd = 0;
			$rc = $GLOBALS['conn']->Execute("
				SELECT COUNT(*) AS QT_COMPL
				  FROM CON_APR_PESSOA
				 WHERE ID_CAD_PESSOA = ?
				   AND ID_TAB_APREND = ?
				   AND DT_ASSINATURA IS NOT NULL
			", array($id, $tabAprID) );
			if (!$rc->EOF):
				$qtd = $rc->fields["QT_COMPL"];
			endif;
			$pctC = floor( ( $qtd / $qtdReq ) * 100);
			$pctP = floor( 100 - $pctC);
			$qtdP = ($qtdReq-$qtd);

			$str .= "<div class=\"row\">";
			$str .= "<div class=\"col-lg-12\">";
			$str .= "<div name=\"progress\" cad-id=\"$id\" req-id=\"$tabAprID\">";
			$str .= "<label class=\"control-label\"><i class=\"".getIconAprendizado( $det["TP_ITEM"], $det["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($det["DS_ITEM"])." ($qtdReq assinaturas)</label>";
			$str .= "<div class=\"progress\" style=\"margin-bottom:0px;cursor:pointer\">";
			$str .= "<div class=\"progress-bar progress-bar-success\" role=\"progressbar\" style=\"width:$pctC%\">$pctC% ($qtd)</div>";
			$str .= "<div class=\"progress-bar progress-bar-danger\" role=\"progressbar\" style=\"width:$pctP%\">$pctP% ($qtdP)</div>";
			$str .= "</div>";
			$str .= "<div id=\"detalhes\" style=\"padding-top:1px;padding-bottom:10px\"></div>";
			$str .= "</div>";
			$str .= "</div>";
			$str .= "</div>";
		endforeach;
		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
	endif;
	
	//CLASSES PENDENTES DE AVALIACAO
	$rs = $GLOBALS['conn']->Execute("
		  SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		   WHERE ta.ID_CAD_PESSOA = ? AND ta.TP_ITEM = ? 
			 AND ta.DT_CONCLUSAO IS NOT NULL 
			 AND ta.DT_AVALIACAO IS NULL 
			 AND ta.DT_INVESTIDURA IS NULL 
			$where
		GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
		ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
	", array( $id, "CL" ) );
	if (!$rs->EOF):
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-warning\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><b>Classes Pendentes de Avalia&ccedil;&atilde;o Regional</b></div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
		
		foreach ($rs as $ks2 => $det):
			$str .= "<i class=\"".getIconAprendizado( $det["TP_ITEM"], $det["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($det["DS_ITEM"])."<br/>";
		endforeach;
		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
	endif;	

	//CLASSES A RECEBER
	$rs = $GLOBALS['conn']->Execute("
			SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
			WHERE ta.ID_CAD_PESSOA = ? AND ta.TP_ITEM = ? 
			 AND ta.DT_CONCLUSAO IS NOT NULL 
			 AND ta.DT_AVALIACAO IS NOT NULL 
			 AND ta.DT_INVESTIDURA IS NULL 
			$where
			GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
			ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
			", array( $id, "CL" ) );
	if (!$rs->EOF):
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-success\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><b>Classes Pendentes de Investidura</b></div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
		
		foreach ($rs as $ks2 => $det):
			$str .= "<i class=\"".getIconAprendizado( $det["TP_ITEM"], $det["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($det["DS_ITEM"])."<br/>";
		endforeach;
		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
	endif;
	
	//ESPECIALIDADES PENDENTES DE AVALIACAO
	$result = $GLOBALS['conn']->Execute("
		SELECT a.CD_AREA_INTERNO, ae.DS_ITEM AS DS_ITEM_AREA, a.CD_ITEM_INTERNO, a.DS_ITEM
		  FROM APR_HISTORICO h
	    INNER JOIN TAB_APRENDIZADO a ON (a.ID = h.ID_TAB_APREND)
		INNER JOIN TAB_APRENDIZADO ae ON (ae.TP_ITEM = a.TP_ITEM AND ae.CD_AREA_INTERNO = a.CD_AREA_INTERNO AND ae.CD_ITEM_INTERNO IS NULL)
		 WHERE h.ID_CAD_PESSOA = ?
		   AND a.TP_ITEM = ?
			 AND h.DT_CONCLUSAO IS NOT NULL 
			 AND h.DT_AVALIACAO IS NULL 
			 AND h.DT_INVESTIDURA IS NULL 
			ORDER BY a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO
	", array( $id, "ES" ) );
	$str .= fGetDetailEspClass("panel-warning","Especialidades Pendentes de Avaliação Regional",$result);
	
	//ESPECIALIDADES A RECEBER
	$result = $GLOBALS['conn']->Execute("
		SELECT a.CD_AREA_INTERNO, ae.DS_ITEM AS DS_ITEM_AREA, a.CD_ITEM_INTERNO, a.DS_ITEM
		  FROM APR_HISTORICO h
	    INNER JOIN TAB_APRENDIZADO a ON (a.ID = h.ID_TAB_APREND)
		INNER JOIN TAB_APRENDIZADO ae ON (ae.TP_ITEM = a.TP_ITEM AND ae.CD_AREA_INTERNO = a.CD_AREA_INTERNO AND ae.CD_ITEM_INTERNO IS NULL)
		 WHERE h.ID_CAD_PESSOA = ?
		   AND a.TP_ITEM = ?
			 AND h.DT_CONCLUSAO IS NOT NULL 
			 AND h.DT_AVALIACAO IS NOT NULL 
			 AND h.DT_INVESTIDURA IS NULL 
			ORDER BY a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO
	", array( $id, "ES" ) );
	$str .= fGetDetailEspClass("panel-success","Especialidades Pendentes de Investidura",$result);

	//ITENS INVESTIDOS
	$result = $GLOBALS['conn']->Execute("
		SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		WHERE ta.ID_CAD_PESSOA = ? AND YEAR(ta.DT_INVESTIDURA) = YEAR(NOW()) $where
		GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
		ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
	", array( $id ) );
	$str .= fGetDetailEspClass("panel-success","&Iacute;tens recebidos",$result);

	$arr["detail"] = $str;
	return $arr;
}

function fGetDetailClass( $class, $titulo, $icon, $result ) {
	$str = "";
	if (!$result->EOF):
		$str .= "<div class=\"col-lg-6 col-xs-6\">";
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel $class\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><i class=\"fa $icon\" aria-hidden=\"true\"></i>&nbsp;$titulo</div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
		$areaAtu = "";
		$first = true;
		foreach ($result as $k => $line):
			if ($line["CD_AP_AREA"] <> $areaAtu):
				$areaAtu = $line["CD_AP_AREA"];
				$str .= "<div class=\"well well-sm\" style=\"padding:4px;margin-bottom:0px;font-size:11px\"><b>".$line["CD_AP_AREA"]." - ".titleCase($line["DS_AP_AREA"])."</b></div>";
				$first = true;
			endif;
			if (!$first):
				$str .= ", ";
			endif;
			$str .= "<span title=\"".$line["DS"]."\">". substr($line["CD_REQ_INTERNO"],-2) ."</span>";
			$first = false;
		endforeach;
		$str .= "</div></div></div></div>";
	endif;
	return $str;
}

function fGetDetailEspClass( $class, $titulo, $result ) {
	$str = "";
	if (!$result->EOF):
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel $class\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><b>$titulo</b></div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
		$areaAtu = "";
		foreach ($result as $k => $line):
			if ($line["DS_ITEM_AREA"] <> $areaAtu):
				$areaAtu = $line["DS_ITEM_AREA"];
				$str .= "<div class=\"well well-sm\" style=\"padding:4px;margin-bottom:0px;font-size:12px\"><b>".titleCase($line["DS_ITEM_AREA"])."</b></div>";
			endif;
			$str .= "&nbsp;<i class=\"".getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] )."\"></i> ".titleCase($line["DS_ITEM"])."<br/>";
		endforeach;
		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
	endif;
	return $str;
}

function getPendentes( $parameters ) {
	$arr = array();
	fConnDB();
	
	$str = "";

	$result = $GLOBALS['conn']->Execute("
		SELECT CD_REQ_INTERNO, CD_AP_AREA, DS_AP_AREA, DS
		  FROM CON_APR_PESSOA
		 WHERE ID_CAD_PESSOA = ?
		   AND ID_TAB_APREND = ?
		   AND DT_ASSINATURA IS NOT NULL
		   AND DT_CONCLUSAO IS NULL
	  ORDER BY CD_REQ_INTERNO
	", array( $parameters["id"], $parameters["req"] ) );
	$str .= fGetDetailClass("panel-success","Itens Conclu&iacute;dos","fa-smile-o",$result);

	$result = $GLOBALS['conn']->Execute("
		SELECT CD_REQ_INTERNO, CD_AP_AREA, DS_AP_AREA, DS
		  FROM CON_APR_PESSOA
		 WHERE ID_CAD_PESSOA = ?
		   AND ID_TAB_APREND = ?
		   AND DT_ASSINATURA IS NULL
		   AND DT_CONCLUSAO IS NULL
	  ORDER BY CD_REQ_INTERNO
	", array( $parameters["id"], $parameters["req"] ) );
	$str .= fGetDetailClass("panel-danger","Itens Pendentes","fa-frown-o",$result);

	$arr["pend"] = $str;
	return $arr;
}

function getEspec( $parameters ) {
	$arr = array();
	fConnDB();
	
	$str = "";
	$rs = $GLOBALS['conn']->Execute("
		SELECT ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QTD
		  FROM APR_HISTORICO h
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = h.ID_TAB_APREND)
    INNER JOIN CON_ATIVOS a ON (a.ID = h.ID_CAD_PESSOA)
		 WHERE ta.TP_ITEM = ?
		   AND ta.CD_AREA_INTERNO = ?
		   AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
	      GROUP BY ta.CD_ITEM_INTERNO, ta.DS_ITEM
	      ORDER BY ta.CD_ITEM_INTERNO
	", array("ES", $parameters["cdArea"] ) );
	$str .= "<div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">";
	foreach ($rs as $ks => $ls):
		$itemInterno = $ls["CD_ITEM_INTERNO"];
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-warning\" aria-expanded=\"false\" style=\"margin:0px\" it-int=\"$itemInterno\">";
		$str .= "<div class=\"panel-heading\" style=\"cursor:pointer\">";
		$str .= "<h5 class=\"panel-title\">$itemInterno ".titleCase($ls["DS_ITEM"]);
		$str .= "<span class=\"badge pull-right\">".$ls["QTD"]."</span>";
		$str .= "</h5>";
		$str .= "</div>";
		$str .= "<div id=\"detalhes\" class=\"panel-body panel-collapse collapse\"></div>";
		$str .= "</div>";
		$str .= "</div>";
	endforeach;
	$str .= "</div>";

	$arr["detail"] = $str;
	return $arr;
}

function getEspecPeople( $parameters ) {
	$arr = array();
	fConnDB();
	
	$str = "";
	$rs = $GLOBALS['conn']->Execute("
		SELECT a.NM
		  FROM APR_HISTORICO h
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = h.ID_TAB_APREND)
 	INNER JOIN CON_ATIVOS a ON (a.ID = h.ID_CAD_PESSOA)
		 WHERE ta.CD_ITEM_INTERNO = ?
		   AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
	      GROUP BY a.NM
	", array( $parameters["item"] ) );
	$i = 0;
	$str .= "<div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">";
	foreach ($rs as $ks => $ls):
		$str .= "<div class=\"row\" style=\"font-size:13px\">".str_pad(++$i, strlen($rs->RecordCount()), "0", STR_PAD_LEFT)." - ".titleCase($ls["NM"])."</div>";
	endforeach;
	$str .= "</div>";

	$arr["people"] = $str;
	return $arr;
}
?>