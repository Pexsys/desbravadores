<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function getGraphData() {
	session_start();
	$pessoaID = $_SESSION['USER']['id_cad_pessoa'];

	$arr = array();
	

	$dtInicio = null;

	//OBJETIVO DO CLUBE
	$result = CONN::get()->Execute("
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
			"dashes"	=> array( "show" => true, "lineWidth" => 1, "dashLength" => array( 10, 30 ) )
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
	$rsCls = CONN::get()->Execute("
		SELECT DISTINCT ID_TAB_APREND, DS_ITEM, CD_COR, CD_ITEM_INTERNO
		  FROM CON_APR_PESSOA 
		 WHERE ID_CAD_PESSOA = ?
		   AND YEAR(DT_INICIO) = YEAR(NOW())
		   AND DT_CONCLUSAO IS NULL
	  ORDER BY CD_ITEM_INTERNO
	", array($pessoaID) );
	if (!$rsCls->EOF):
		$arr["checkbox"] = array();

		foreach($rsCls as $j => $lnc):
			$result = CONN::get()->Execute("
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
			", array($pessoaID, $lnc["ID_TAB_APREND"]) );
			if (!$result->EOF):
				$qtDt = 0;

				$arr["checkbox"][$lnc["ID_TAB_APREND"]] = array( 
					"label"		=> "&nbsp;".titleCase($lnc["DS_ITEM"]), 
					"data"		=> array(), 
					"color"		=> $lnc["CD_COR"], 
					"lines"		=> array( "show" => true, "fill" => true )
				);
				$arr["checkbox"][$lnc["ID_TAB_APREND"]]["data"][] = array(strtotime($dtInicio)."000", $qtDt );

				foreach($result as $k => $line):
					$qtDt += $line["QT"];
					$arr["checkbox"][$lnc["ID_TAB_APREND"]]["data"][] = array( 
						strtotime($line["DT_ASSINATURA"])."000",
						floor((100/$line["QTD"])*$qtDt)
					);
				endforeach;
			endif;
		endforeach;
	endif;
	return $arr;
}

function getMestrados(){
	session_start();
	
	
	$arr = array();
	$rg = CONN::get()->Execute("
	    SELECT DISTINCT car.ID
	      FROM CON_APR_REQ car
   LEFT JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID AND ah.ID_CAD_PESSOA = ?)
	     WHERE car.CD_AREA_INTERNO = 'ME'
	  ORDER BY car.CD_ITEM_INTERNO
	", array( $_SESSION['USER']['id_cad_pessoa']) );
	foreach ($rg as $lg => $fg):
		$arr[] = $fg["ID"];
	endforeach;
	return array( "id" => $_SESSION['USER']['id_cad_pessoa'], "rules" => $arr );
}

function getClassPainelMestrado( $pct ){
	if ($pct < 25):
		return array( "panel" => "panel-default", "title" => "type-default" );
	elseif ($pct < 50):
		return array( "panel" => "panel-info", "title" => "type-info" );
	elseif ($pct < 75):
		return array( "panel" => "panel-yellow", "title" => "type-warning" );
	elseif ($pct < 100):
		return array( "panel" => "panel-red", "title" => "type-danger" );
	else:
		return array( "panel" => "panel-success", "title" => "type-success" );
	endif;
}

function getPainelMestrado( $parameters ){
	return getPainelMestradoPessoa( $parameters["ruleID"], $parameters["id"] );
}

function getPainelMestradoPessoa( $ruleID, $pessoaID ){
	//LE REGRAS
	$rg = CONN::get()->Execute("
	 	SELECT DISTINCT car.CD_ITEM_INTERNO, car.CD_AREA_INTERNO, car.DS_ITEM, car.TP_ITEM, car.MIN_AREA, ah.DT_CONCLUSAO, ca.ID_CAD_MEMBRO
	 	  FROM CON_APR_REQ car
	 LEFT JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID AND ah.ID_CAD_PESSOA = ?)
	 LEFT JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ?)
	 	WHERE car.ID = ?
	 ", array( $pessoaID, $pessoaID, $ruleID ) );
	$fg = $rg->fields;
	 
	$min = $fg["MIN_AREA"];
	
	$feitas = 0;
	//LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
	$rR = CONN::get()->Execute("
		 SELECT tar.ID, tar.QT_MIN, COUNT(*) AS QT_FEITAS
		 FROM TAB_APR_ITEM tar
		 INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_ITEM = tar.ID AND car.TP_ITEM_RQ = 'ES')
		 INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
		 WHERE tar.ID_TAB_APREND = ?
		 GROUP BY tar.ID, tar.QT_MIN
	", array( $pessoaID, $ruleID ) );
	foreach($rR as $lR => $fR):
	 	$feitas += min( $fR["QT_MIN"], $fR["QT_FEITAS"] );
	endforeach;
	
	$icon = getIconAprendizado( $fg["TP_ITEM"], $fg["CD_AREA_INTERNO"], "fa-4x" );
	$area = getMacroArea( $fg["TP_ITEM"], $fg["CD_AREA_INTERNO"] );
	$pct = floor( ( $feitas / $min ) * 100 );
	
	$advise = null;
	$class = array( "panel" => "panel-default", "title" => "type-default" );
	if ( $pct < 100 && !is_null($fg["DT_CONCLUSAO"]) ):
	 	$class = array( "panel" => "panel-primary", "title" => "type-primary" );
	 	$advise = "Concluído pela regra antiga. Atualize seu mestrado para a regra nova.";
	else:
	 	$class = getClassPainelMestrado( $pct );
	endif;
	$sizeClass = "col-md-6 col-xs-12 col-sm-6 col-lg-4 col-xl-3";
	
	$fields = array(
		 "name" => "detail",
		 "what" => "rules",
		 "cl-bar" => $class["title"],
	 	"id-rule" => $ruleID
	);
	
	//VERIFICA REQUISITOS CUMPRIDOS, MAS AINDA NÃO FINALIZADO.
	if ( $pct >= 100 ):
		 $rI = CONN::get()->Execute("
			 SELECT DT_CONCLUSAO
			 FROM APR_HISTORICO
			 WHERE ID_CAD_PESSOA = ?
			 AND ID_TAB_APREND = ?
		 ", array( $pessoaID, $ruleID ) );
		 if ($rI->EOF || is_null($rI->fields["DT_CONCLUSAO"]) ):
		 	$class = array( "panel" => "panel-green", "title" => "type-success" );
		 	$sizeClass = "col-md-4 col-xs-12 col-sm-6 col-xl-3 col-lg-4 blink";
		
			 //INSERE NOTIFICAÇOES SE NÃO EXISTIR.
			 CONN::get()->Execute("
				 INSERT INTO LOG_MENSAGEM ( ID_ORIGEM, TP, ID_CAD_USUARIO, EMAIL, DH_GERA )
				 SELECT ?, 'M', cu.ID, ca.EMAIL, NOW()
				 FROM CON_ATIVOS ca
				 INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
				 WHERE ca.ID = ?
				 AND NOT EXISTS (SELECT 1 FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = 'M' AND ID_CAD_USUARIO = cu.ID)
			 ", array( $fg["ID"], $pessoaID, $ruleID ) );
			
			 $fields = array(
				 "name" => "print",
				 "what" => "capa",
				 "id-pess" => $pessoaID,
				 "id-membro" => $fg["ID_CAD_MEMBRO"],
				 "cd-item" => $fg["CD_ITEM_INTERNO"]
			 );
		endif;
	endif;
	 
	return fItemAprendizado(array(
	 	 "classPanel" 	=> $class["panel"],
		 "leftIcon"		=> $icon,
		 "value"			=> $fg["CD_ITEM_INTERNO"],
		 "title"			=> titleCase( substr($fg["DS_ITEM"],12), array(" "), array("ADRA", "em", "e") ) . "<br/>$feitas / $min",
		 "strBL"			=> $advise,
		 "fields"			=> $fields,
		 "classSize"		=> $sizeClass
	));
}


function getMasterRules( $parameters ) {
	session_start();
	return getMasterRulesPessoa( $parameters["id"], $_SESSION['USER']['id_cad_membro'] );
}
	
function getMasterRulesPessoa( $ruleID, $cadMembroID ){
	$title = "";
	$message = "";
	
	
	//LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
	$rR = CONN::get()->Execute("
		SELECT taq.ID, taq.QT_MIN, ta.DS_ITEM
		  FROM TAB_APR_ITEM taq
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = taq.ID_TAB_APREND)
		 WHERE taq.ID_TAB_APREND = ?
    ", array( $ruleID ) );
	
	$title = "<b>".titleCase( $rR->fields["DS_ITEM"], array(" "), array("ADRA", "em", "e") )."</b>";
	
	foreach($rR as $lR => $fR):
		$arr[ $fR["ID"] ] = array(
			"min" => $fR["QT_MIN"],
			"hist" => array()
		);
		//ADICIONAR REGRA E SELECAO DA REGRA.
		//LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
		$rS = CONN::get()->Execute("
			SELECT car.CD_ITEM_INTERNO_RQ, car.DS_ITEM_RQ, ah.DT_INICIO, ah.DT_CONCLUSAO
			FROM CON_APR_REQ car
			LEFT JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = (SELECT ID_CAD_PESSOA FROM CAD_MEMBRO WHERE ID = ?) )
			WHERE car.ID_TAB_APR_ITEM = ?
			ORDER BY car.DS_ITEM_RQ
        ", array( $cadMembroID, $fR["ID"] ) );
		foreach($rS as $lS => $fS):
			$arr[ $fR["ID"] ]["hist"][] = $fS;
		endforeach;
	endforeach;
		
	$message .= "";
	$req = 0;
	$seq = 0;
	foreach ($arr as $k => $i):
		++$req;
		$plus = 0;
		
		$list = "";
		//ADICIONA ITENS DO REQUISITO
		foreach ($i["hist"] as $j => $z):
			++$seq;
			$cdItem = $z['CD_ITEM_INTERNO_RQ'];
			$dsItem = titleCase($z['DS_ITEM_RQ'])." ($cdItem)";
			if (!is_null($z['DT_CONCLUSAO'])):
				++$plus;
				$dsItem = "<mark><b>$dsItem</b>&nbsp;<sup>$plus</sup></mark>";
			elseif (!is_null($z['DT_INICIO'])):
				$dsItem = "<u>$dsItem</u>";
			else:
				$dsItem = "<a style=\"cursor:pointer\" name=\"print\" what=\"capa\" id-membro=\"$cadMembroID\" cd-item=\"$cdItem\">$dsItem</a>";
			endif;
			$list .= "<div class=\"col-sm-6\">$seq) ".$dsItem."</div>";
		endforeach;
		
		$message .= "<div class=\"row\"><div class=\"col-sm-8\"><b>". ($req > 1 ? "e ter ": "Ter "). $i["min"] ." das seguintes especialidades:</b></div><div class=\"col-sm-4\"><mark class=\"pull-right\">Completadas: <b>$plus</b></mark></div></div>";
		$message .= "<div class=\"row\">";
		$message .= $list;
		$message .= "</div><br/><br/>";
	endforeach;
	$message .= "";
	
	return array( "return" => true, "title" => $title, "message" => $message );
}
?>