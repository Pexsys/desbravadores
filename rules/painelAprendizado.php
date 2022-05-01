<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getGraphData() {
	session_start();

	$arr = array();
	/*
	$qtdRegulares = 0;
	$result = CONN::get()->execute("
		SELECT COUNT(*) AS QT
		  FROM TAB_APRENDIZADO 
		 WHERE CD_ITEM_INTERNO LIKE '01%00'
		   AND TP_ITEM = 'CL'
	");
	if (!$result->EOF):
		$qtdRegulares = $result->fields['QT'];
	endif;

	$qtdAvancadas = 0;
	$result = CONN::get()->execute("
		SELECT COUNT(*) AS QT
		  FROM TAB_APRENDIZADO 
		 WHERE CD_ITEM_INTERNO LIKE '01%01'
		   AND TP_ITEM = 'CL'
	");
	if (!$result->EOF):
		$qtdAvancadas = $result->fields['QT'];
	endif;

	//ANALISE GRAFICA DAS CLASSES COMPLETADAS
	$arr["clsC"] = array();
	$result = CONN::get()->execute("
		SELECT a.TP_ITEM, a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO, a.DS_ITEM, a.CD_COR, COUNT(*) AS QTD
		FROM APR_HISTORICO h
		INNER JOIN TAB_APRENDIZADO a ON (a.ID = h.ID_TAB_APREND)
		WHERE a.TP_ITEM = 'CL'
		AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
		GROUP BY a.TP_ITEM, a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO, a.DS_ITEM, a.CD_COR
		ORDER BY a.CD_ITEM_INTERNO
	");
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
	$result = CONN::get()->execute("
		SELECT COUNT(*) AS QT_RG
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%00'
				   AND YEAR(ah.DT_AVALIACAO) < YEAR(NOW())
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID_CAD_PESSOA)
		WHERE (a.QT IS NULL OR a.QT < ?) 
		  AND at.FG_REU_SEM = 'S'
	", $qtdRegulares );
	if (!$result->EOF):
		$aCompletar = $result->fields['QT_RG'];
	endif;

	//CLASSES REGULARES COMPLETADAS
	$aCompletadas = 0;
	$result = CONN::get()->execute("
		SELECT COUNT(*) AS QT_RG_OK
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%00'
				   AND (YEAR(ah.DT_CONCLUSAO) = YEAR(NOW()) OR YEAR(ah.DT_INVESTIDURA) = YEAR(NOW()))
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID_CAD_PESSOA)
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
	$result = CONN::get()->execute("
		SELECT COUNT(*) AS QT_AV
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%01'
				   AND YEAR(ah.DT_AVALIACAO) < YEAR(NOW())
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID_CAD_PESSOA)
		WHERE (a.QT IS NULL OR a.QT < ?)
		  AND at.FG_REU_SEM = 'S'
	", $qtdRegulares );
	if (!$result->EOF):
		$aCompletar = $result->fields['QT_AV'];
	endif;

	//CLASSES AVANCADAS COMPLETADAS
	$aCompletadas = 0;
	$result = CONN::get()->execute("
		SELECT COUNT(*) AS QT_AV_OK
		  FROM CON_ATIVOS at
	 LEFT JOIN (SELECT ah.ID_CAD_PESSOA, COUNT(*) AS QT
				  FROM APR_HISTORICO ah 
			INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
			INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND ca.FG_REU_SEM = 'S')
				 WHERE ta.CD_ITEM_INTERNO LIKE '01%01'
				   AND (YEAR(ah.DT_CONCLUSAO) = YEAR(NOW()) OR YEAR(ah.DT_INVESTIDURA) = YEAR(NOW()))
			  GROUP BY ah.ID_CAD_PESSOA) AS a ON (a.ID_CAD_PESSOA = at.ID_CAD_PESSOA)
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
	*/

	$a1 = 50;
	$a2 = 80;
	$a3 = 99;

	$result = CONN::get()->execute("
		SELECT ta.ID, ta.CD_COR,

				(SELECT COUNT(*)
				FROM (
					SELECT ID_TAB_APREND, FLOOR((QTD/QT_TOTAL)*100) AS PCT_CMPL
					FROM (
						SELECT 
							ah.ID_TAB_APREND,
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
						AND cai.CD_ITEM_INTERNO LIKE '01%'
					) AS x
				) AS y
				WHERE y.PCT_CMPL <= ?
					AND y.ID_TAB_APREND = ta.ID) AS QT_1,

				(SELECT COUNT(*)
				FROM (
					SELECT ID_TAB_APREND, FLOOR((QTD/QT_TOTAL)*100) AS PCT_CMPL
					FROM (
						SELECT 
							ah.ID_TAB_APREND,
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
						AND cai.CD_ITEM_INTERNO LIKE '01%'
					) AS x
				) AS y
				WHERE (y.PCT_CMPL > ? AND y.PCT_CMPL <= ?)
				AND y.ID_TAB_APREND = ta.ID) AS QT_2,

				(SELECT COUNT(*)
				FROM (
					SELECT ID_TAB_APREND, FLOOR((QTD/QT_TOTAL)*100) AS PCT_CMPL
					FROM (
						SELECT 
							ah.ID_TAB_APREND,
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
						AND cai.CD_ITEM_INTERNO LIKE '01%'
					) AS x
				) AS y
				WHERE (y.PCT_CMPL > ? AND y.PCT_CMPL <= ?)
				AND y.ID_TAB_APREND = ta.ID) AS QT_3,

				(SELECT COUNT(*)
					FROM APR_HISTORICO 
					WHERE ID_TAB_APREND = ta.ID
					AND (
							(YEAR(DT_INICIO) = YEAR(NOW()) AND YEAR(DT_CONCLUSAO) = YEAR(NOW()))
							OR
							( YEAR(DT_INICIO) < YEAR(NOW()) AND YEAR(DT_CONCLUSAO) = YEAR(NOW()) AND YEAR(DT_INVESTIDURA) = YEAR(NOW()) )
						) ) AS QT_OK

			FROM TAB_APRENDIZADO ta
			WHERE ta.CD_ITEM_INTERNO LIKE '01%'
		ORDER BY ta.CD_ITEM_INTERNO
	", array( $a1, $a1, $a2, $a2, $a3 ) );
	foreach ($result as $k => $f):
		if ( ($f['QT_1'] + $f['QT_2'] + $f['QT_3'] + $f['QT_OK']) > 0):
			$arr[] = array(
				"Aprend" => PATTERNS::toConvert($f['ID']),
				"color" => $f['CD_COR'],
				"freq"	=> array(
					"low" => ($f['QT_1'] * 1),
					"mid" => ($f['QT_2'] * 1),
					"alm" => ($f['QT_3'] * 1),
					"full" => ($f['QT_OK'] * 1)
				)
			);
		endif;
	endforeach;
	return $arr;
}

function getClasses( $parameters ) {
	$arr = array();
	$id = $parameters["id"];
	
	$where = "";
	if (!empty($parameters["iil"])):
		$where .= " AND ta.CD_ITEM_INTERNO LIKE '".$parameters["iil"]."%'";
	endif;

	$str = "";
	$str .= "<div class=\"col-lg-12\">";
	
	//CLASSES EM ANDAMENTO
	$rs = CONN::get()->execute("
		  SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		   WHERE ta.ID_CAD_PESSOA = ? AND ta.TP_ITEM = 'CL' AND ta.DT_CONCLUSAO IS NULL $where
		GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
		ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
	", array( $id ) );
	if (!$rs->EOF):
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-warning\" style=\"padding-bottom:5px\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><b>Classes em Andamento</b></div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
				
		foreach ($rs as $ks2 => $det):
			$qtdReq = $det["QT_REQ"];
			$tabAprID = $det["ID_TAB_APREND"];
			$pct = 0;
			$qtd = 0;
			$rc = CONN::get()->execute("
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
			$str .= "<div name=\"progress\" cad-id=\"$id\" req-id=\"$tabAprID\" style=\"padding-top:10px\">";
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
	$rs = CONN::get()->execute("
		  SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		   WHERE ta.ID_CAD_PESSOA = ? AND ta.TP_ITEM = 'CL'
			 AND ta.DT_CONCLUSAO IS NOT NULL 
			 AND ta.DT_AVALIACAO IS NULL 
			 AND ta.DT_INVESTIDURA IS NULL 
			$where
		GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM
		ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
	", array( $id ) );
	if (!$rs->EOF):
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-warning\" style=\"padding-bottom:5px\">";
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
	$rs = CONN::get()->execute("
			SELECT DISTINCT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM,
				IF(cc.FG_COMPRA = 'S' OR ccag.ID IS NOT NULL,'OK','NOK') AS TP_BUY
			FROM CON_APR_PESSOA ta
			INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID_TAB_APREND)
			LEFT JOIN CON_COMPRAS cc ON (cc.ID_CAD_PESSOA = ta.ID_CAD_PESSOA AND cc.FG_COMPRA = 'S' AND cc.ID_TAB_APREND = ta.ID_TAB_APREND)
			LEFT JOIN CON_COMPRAS ccag ON (ccag.ID_CAD_PESSOA = ta.ID_CAD_PESSOA AND ccag.FG_COMPRA = 'S' AND ccag.ID_TAB_APREND = tm.ID_AGRUPADA)
			WHERE ta.ID_CAD_PESSOA = ? AND ta.TP_ITEM = 'CL'
			 AND ta.DT_CONCLUSAO IS NOT NULL 
			 AND ta.DT_AVALIACAO IS NOT NULL 
			 AND ta.DT_INVESTIDURA IS NULL 
			$where
			ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
			", array( $id ) );
	if (!$rs->EOF):
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel panel-success\" style=\"padding-bottom:5px\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><b>Classes Pendentes de Investidura</b></div>";
		$str .= "<div class=\"panel-body\" style=\"padding:5px 10px\">";
		
		foreach ($rs as $ks2 => $det):
			$str .= "<i class=\"".getIconAprendizado( $det["TP_ITEM"], $det["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($det["DS_ITEM"]);
			$str .= "<i class=\"fas ".($det["TP_BUY"] == "OK"?"fa-info-circle":"fa-question-circle")." pull-right\" title=\"".($det["TP_BUY"] == "OK"?"Item comprado":"Item ainda não comprado")."\" style=\"".($det["TP_BUY"] == "OK"?"color:green":"color:red")."\"></i>";
			$str .= "<br/>";
		endforeach;
		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
	endif;
	
	//ESPECIALIDADES PENDENTES DE AVALIACAO
	$result = CONN::get()->execute("
		SELECT a.CD_AREA_INTERNO, ae.DS_ITEM AS DS_ITEM_AREA, a.CD_ITEM_INTERNO, a.DS_ITEM
		  FROM APR_HISTORICO h
	    INNER JOIN TAB_APRENDIZADO a ON (a.ID = h.ID_TAB_APREND)
		INNER JOIN TAB_APRENDIZADO ae ON (ae.TP_ITEM = a.TP_ITEM AND ae.CD_AREA_INTERNO = a.CD_AREA_INTERNO AND ae.CD_ITEM_INTERNO IS NULL)
		 WHERE h.ID_CAD_PESSOA = ?
		   AND a.TP_ITEM = 'ES'
			 AND h.DT_CONCLUSAO IS NOT NULL 
			 AND h.DT_AVALIACAO IS NULL 
			 AND h.DT_INVESTIDURA IS NULL 
			ORDER BY a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO
	", array( $id ) );
	$str .= fGetDetailEspClass("panel-warning","Especialidades Pendentes de Avaliação Regional",$result);
	
	//ESPECIALIDADES A RECEBER
	$result = CONN::get()->execute("
		SELECT a.CD_AREA_INTERNO, ae.DS_ITEM AS DS_ITEM_AREA, a.CD_ITEM_INTERNO, a.DS_ITEM,
				IF(cc.FG_COMPRA = 'S' OR cc.ID IS NOT NULL,'OK','NOK') AS TP_BUY
		  FROM APR_HISTORICO h
	    INNER JOIN TAB_APRENDIZADO a ON (a.ID = h.ID_TAB_APREND)
		INNER JOIN TAB_APRENDIZADO ae ON (ae.TP_ITEM = a.TP_ITEM AND ae.CD_AREA_INTERNO = a.CD_AREA_INTERNO AND ae.CD_ITEM_INTERNO IS NULL)
		LEFT JOIN CON_COMPRAS cc ON (cc.ID_CAD_PESSOA = h.ID_CAD_PESSOA AND cc.FG_COMPRA = 'S' AND cc.ID_TAB_APREND = h.ID_TAB_APREND)
		WHERE h.ID_CAD_PESSOA = ?
		   AND a.TP_ITEM = 'ES'
			 AND h.DT_CONCLUSAO IS NOT NULL 
			 AND h.DT_AVALIACAO IS NOT NULL 
			 AND h.DT_INVESTIDURA IS NULL 
			ORDER BY a.CD_AREA_INTERNO, a.CD_ITEM_INTERNO
	", array( $id) );
	$str .= fGetDetailEspClass("panel-success","Especialidades Pendentes de Investidura",$result);

	//ITENS INVESTIDOS
	$result = CONN::get()->execute("
		SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_AREA_INTERNO, ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA ta
		WHERE ta.ID_CAD_PESSOA = ? AND YEAR(ta.DT_INVESTIDURA) = YEAR(NOW())
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
		$str .= "<div class=\"col-lg-12 col-xs-12 col-md-12 col-sm-12\">";
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel $class\" style=\"margin-bottom:1px\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\"><i class=\"fas $icon\" aria-hidden=\"true\"></i>&nbsp;$titulo</div>";
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
			$str .= "&nbsp;<i class=\"".getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] )."\"></i> ".titleCase($line["DS_ITEM"]);
			if (isset($line["TP_BUY"])):
				$str .= "&nbsp;&nbsp;<i class=\"fas ".($line["TP_BUY"] == "OK"?"fa-info-circle":"fa-question-circle")."\" title=\"".($line["TP_BUY"] == "OK"?"Item comprado":"Item ainda não comprado")."\" style=\"".($line["TP_BUY"] == "OK"?"color:green":"color:red")."\"></i>";
			endif;
			$str .= "<br/>";
		endforeach;
		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
	endif;
	return $str;
}

function getPendentes( $parameters ) {
	$arr = array();
	$str = "";

	$result = CONN::get()->execute("
		SELECT CD_REQ_INTERNO, CD_AP_AREA, DS_AP_AREA, DS
		FROM CON_APR_PESSOA
		WHERE ID_CAD_PESSOA = ?
		AND ID_TAB_APREND = ?
		AND DT_ASSINATURA IS NULL
		AND DT_CONCLUSAO IS NULL
	ORDER BY CD_REQ_INTERNO
	", array( $parameters["id"], $parameters["req"] ) );
	$str .= fGetDetailClass("panel-danger","Itens Pendentes","fa-frown-o",$result);

	$result = CONN::get()->execute("
		SELECT CD_REQ_INTERNO, CD_AP_AREA, DS_AP_AREA, DS
		  FROM CON_APR_PESSOA
		 WHERE ID_CAD_PESSOA = ?
		   AND ID_TAB_APREND = ?
		   AND DT_ASSINATURA IS NOT NULL
		   AND DT_CONCLUSAO IS NULL
	  ORDER BY CD_REQ_INTERNO
	", array( $parameters["id"], $parameters["req"] ) );
	$str .= fGetDetailClass("panel-success","Itens Conclu&iacute;dos","fa-smile-o",$result);


	$arr["pend"] = $str;
	return $arr;
}

function getEspec( $parameters ) {
	$arr = array();

	$str = "";
	$rs = CONN::get()->execute("
		SELECT ta.CD_ITEM_INTERNO, ta.DS_ITEM, COUNT(*) AS QTD
		  FROM APR_HISTORICO h
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = h.ID_TAB_APREND)
    INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
		 WHERE ta.TP_ITEM = 'ES'
		   AND ta.CD_AREA_INTERNO = ?
		   AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
	      GROUP BY ta.CD_ITEM_INTERNO, ta.DS_ITEM
	      ORDER BY ta.CD_ITEM_INTERNO
	", array($parameters["cdArea"] ) );
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

	$str = "";
	$rs = CONN::get()->execute("
		SELECT a.NM
		  FROM APR_HISTORICO h
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = h.ID_TAB_APREND)
 	INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
		 WHERE ta.CD_ITEM_INTERNO = ?
		   AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
	      GROUP BY a.NM
	", array( $parameters["item"] ) );
	$i = 0;
	$str .= "<div class=\"col-sm-12 col-xs-12 col-md-12 col-lg-12\">";
	foreach ($rs as $ks => $ls):
		$str .= "<div class=\"row\" style=\"font-size:13px\">".fStrZero(++$i, strlen($rs->RecordCount()))." - ".titleCase($ls["NM"])."</div>";
	endforeach;
	$str .= "</div>";

	$arr["people"] = $str;
	return $arr;
}
?>
