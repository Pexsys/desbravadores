<?php
@require_once("historico.php");

function marcaRequisitoID( $assDT, $pessoaID, $histID, $reqID ) {

	//APAGA CASO DATA DE ASSINATURA NULA
	if ( is_null($assDT) || empty($assDT) ):
		$GLOBALS['conn']->Execute("
			DELETE FROM APR_PESSOA_REQ
			WHERE ID_HISTORICO = ?
			  AND ID_TAB_APR_ITEM = ?
		", array( $histID, $reqID ) );

	else:
		//RECUPERA REQUISITO COM ITEM APRENDIZADO
		$rs = $GLOBALS['conn']->Execute("
		  SELECT 1
			FROM APR_PESSOA_REQ
		   WHERE ID_HISTORICO = ?
			 AND ID_TAB_APR_ITEM = ?
		", array( $histID, $reqID ) );
		
		//SE NAO EXISTIR, INSERE
		if ($rs->EOF):
			$GLOBALS['conn']->Execute("
				INSERT INTO APR_PESSOA_REQ(
					ID_HISTORICO,
					ID_TAB_APR_ITEM,
					DT_ASSINATURA
				) VALUES (
					?,?,?
				)
			", array( $histID, $reqID, $assDT ) );
			
		//ATUALIZA
		else:
			$GLOBALS['conn']->Execute("
				UPDATE APR_PESSOA_REQ
				   SET DT_ASSINATURA = ?
				 WHERE ID_HISTORICO = ?
				   AND ID_TAB_APR_ITEM ?
				   AND DT_ASSINATURA <> ?
			", array( $assDT, $histID, $reqID, $assDT ) );
		endif;
	endif;
}

function marcaRequisito( $pessoaID, $itemID, $reqCD, $assDT ) {
	$arr = array("idreq" => null);
	
	//VERIFICA SE REQUISITO EH REGULAR OU AVANCADA.
	if ( fStrStartWith($reqCD,"XX") ):
		if ($itemID == 1 || $itemID == 3 || $itemID == 5 || $itemID == 7 || $itemID == 9 || $itemID == 11):
			$itemID++;
		endif;
		$reqCD = substr($reqCD,-2);
	endif;
	
	//RECUPERA O ID DO ITEM_APRENDIZADO, BASEADO NO CODIGO DE BARRAS E REQUISITO APONTADO.
	$rs = $GLOBALS['conn']->Execute("
		SELECT tai.ID
		FROM TAB_APR_ITEM tai
	  INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = tai.ID_TAB_ITEM)
		   WHERE ta.ID = ?
		 AND tai.CD_REQ_INTERNO = ?
		", array( $itemID, $reqCD ) );

	if (!$rs->EOF):
		$arr["idreq"] = $rs->fields['ID'];
		
		$uh = updateHistorico( 
			$pessoaID, 
			$itemID, 
			array(
				"dt_inicio" => "N",
				"dt_conclusao" => "N",
				"dt_avaliacao" => "N",
				"dt_investidura" => "N"
			), 
			null
		);
		
		marcaRequisitoID( $assDT, $pessoaID, $uh["id"], $arr["idreq"] );
	endif;
	return $arr;
}

function getAcompanhamento( $param ) {
	$str = "<input type=\"hidden\" field=\"id_cad_pessoa\" value=\"".$param["ip"]."\"/>";
	
	$cap = consultaAprendizadoPessoa( $param["it"], $param["ip"] );
	$str .= fGetClassAcomp( $cap, $param );
	
	if ($cap["ar"] == "REGULAR"):
		$param["it"]++;
		$cap = consultaAprendizadoPessoa( $param["it"], $param["ip"] );
		$str .= fGetClassAcomp( $cap, $param );
	endif;

	return array( "logged" => true, "result" => $str );
}

function fGetClassAcomp( $cap, $param ){
	$strC = "";
	
	$result = $GLOBALS['conn']->Execute("
		   SELECT taa.CD AS CD_AP_AREA, taa.DS AS DS_AP_AREA, 
				  ta.TP_ITEM, ta.CD_COR, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO, ta.DS_ITEM, 
				  tai.ID, tai.CD_REQ_INTERNO,
				  ah.DT_CONCLUSAO, 
				  apr.DT_ASSINATURA
			 FROM TAB_APRENDIZADO ta
		LEFT JOIN TAB_APR_ITEM tai ON ( tai.ID_TAB_ITEM = ta.ID ) 
		LEFT JOIN TAB_APR_AREA taa ON ( taa.ID = tai.ID_TAB_APR_AREA ) 
		LEFT JOIN APR_HISTORICO ah ON ( ah.ID_TAB_APREND = ta.ID AND ah.ID_CAD_PESSOA = ? )
		LEFT JOIN APR_PESSOA_REQ apr ON (apr.ID_HISTORICO = ah.ID AND apr.ID_TAB_APR_ITEM = tai.ID)
			WHERE ta.ID = ?
		      AND apr.DT_ASSINATURA IS NULL
		      AND ah.DT_CONCLUSAO IS NULL
	     ORDER BY tai.CD_REQ_INTERNO
	", array( $param["ip"], $param["it"] ) );
	$strC .= fGetDetailAcomp("panel-danger","Itens Pendentes","fa-frown-o",$result);	
	
	$result = $GLOBALS['conn']->Execute("
		   SELECT taa.CD AS CD_AP_AREA, taa.DS AS DS_AP_AREA, 
				  ta.TP_ITEM, ta.CD_COR, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO, ta.DS_ITEM, 
				  tai.ID, tai.CD_REQ_INTERNO,
				  ah.DT_CONCLUSAO, 
				  apr.DT_ASSINATURA
			 FROM TAB_APRENDIZADO ta
		LEFT JOIN TAB_APR_ITEM tai ON ( tai.ID_TAB_ITEM = ta.ID ) 
		LEFT JOIN TAB_APR_AREA taa ON ( taa.ID = tai.ID_TAB_APR_AREA ) 
		LEFT JOIN APR_HISTORICO ah ON ( ah.ID_TAB_APREND = ta.ID AND ah.ID_CAD_PESSOA = ? )
		LEFT JOIN APR_PESSOA_REQ apr ON (apr.ID_HISTORICO = ah.ID AND apr.ID_TAB_APR_ITEM = tai.ID)
			WHERE ta.ID = ?
		      AND apr.DT_ASSINATURA IS NOT NULL
		      AND ah.DT_CONCLUSAO IS NULL
	     ORDER BY tai.CD_REQ_INTERNO
	", array( $param["ip"], $param["it"] ) );
	$strC .= fGetDetailAcomp("panel-success","Itens Conclu&iacute;dos","fa-smile-o",$result);
	
	$str = "<div class=\"row\">";
	$str .= "<div class=\"well well-sm\" style=\"padding:4px;margin-bottom:0px;font-size:10px;color:". ($param["it"] > 10 ? "#000000" : "#ffffff") .";background-color:".$cap["cr"]."\">";
	$str .= "<b>".$cap["nm"]."<br/>".titleCase($cap["ap"]). (empty($strC) ? "<i class=\"fa fa-check pull-left\"></i> - CLASSE JÁ CONCLUÍDA" : "") ."</b></div>";
	$str .= (empty($strC) ? "" : $strC);
	$str .= "</div>";
	return $str;
}

function fGetDetailAcomp($class, $titulo, $icon, $result){
	$str = "";
	if (!$result->EOF):
		$str .= "<div class=\"col-lg-6 col-xs-6\">";
		$str .= "<div class=\"row\">";
		$str .= "<div class=\"panel $class\">";
		$str .= "<div class=\"panel-heading\" style=\"padding:3px 10px\">$titulo</div>";
		$str .= "<div class=\"panel-body\" style=\"height:100px;overflow-y:auto;overflow-x:hidden;padding:5px 10px\">";
		$areaAtu = "";
		$first = true;
		foreach ($result as $k => $line):
			if ($line["CD_AP_AREA"] <> $areaAtu):
				$areaAtu = $line["CD_AP_AREA"];
				$str .= "<div class=\"well well-sm\" style=\"padding:4px;margin-bottom:0px;font-size:11px\"><b>".$line["CD_AP_AREA"]." - ".titleCase($line["DS_AP_AREA"])."</b></div>";
				$first = true;
			endif;
			$checked = ($class == "panel-success");
			
			$req = $line["CD_REQ_INTERNO"];
			$taiID = $line["ID"];
			$rTela = substr($req,-2);
			
			$str .= "<div class=\"row\">";
			$str .= "<div class=\"col-sm-6\" style=\"padding:1px 0px 2px 15px\">";
			$str .= "<label>$rTela</label> <input type=\"checkbox\" for=\"dt-req-$taiID\"". ($checked?" checked":"")." value-on=\"S\" value-off=\"N\" data-on=\"Conclu&iacute;do\" data-off=\"Pendente\" data-onstyle=\"success\" data-offstyle=\"danger\" data-toggle=\"toggle\" data-width=\"85\" data-size=\"small\" data-style=\"quick\"/>";
			$str .= "</div>";
			$str .= "<div class=\"col-sm-6\" style=\"1px 15px 2px 10px\">";
			
			$dateValue = "";
			$styleDate = "";
			if ($checked):
				$dateValue = date( 'd/m/Y', strtotime($line["DT_ASSINATURA"]) );
			else:
				$styleDate = " style=\"display:none\"";
			endif;
			
			$str .= "<input type=\"text\" name=\"dt_assinatura\" field=\"dt-req-$taiID\" class=\"form-control input-sm date\" value=\"$dateValue\" placeholder=\"Assinatura\"$styleDate/>";
			$str .= "</div>";
			$str .= "</div>";
			$first = false;
		endforeach;
		$str .= "</div></div></div></div>";
	endif;
	return $str;
}
?>