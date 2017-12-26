<?php
@require_once("../include/functions.php");
@require_once("../include/acompanhamento.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function barcode( $parameters ) {
	session_start();
	
	$arr = array();
	$arr['result'] = "";
	$arr['logged'] = false;
	
	$temPerfil = isset($_SESSION['USER']['ssid']);
	if (!$temPerfil):
		session_destroy();
		$arr['logout'] = true;
		return $arr;
		exit;
	endif;
	
	$brdt =  mb_strtoupper($parameters["brdt"]);
	
	//Verificacao de Usuario/Senha
	if ( isset($brdt) && !empty($brdt) ):
		$frm		= $parameters["frm"];
		$op			= $frm["op"];

		$barDecode	= $GLOBALS['pattern']->getBars()->decode($brdt);
		
		fConnDB();
		if ( $barDecode["ci"] !== $GLOBALS['pattern']->getBars()->getClubeID() || 
			$barDecode["lg"] <> $GLOBALS['pattern']->getBars()->getLength() ):
			$arr['result'] = "Código não pertence ao ".$GLOBALS['pattern']->getClubeDS(array("cl","nm"));

		//CHAMADA
		elseif ( $op == "CHAMADA" ):
			$arr = setChamada($barDecode);
			
		//APRENDIZADO
		elseif ( $op == "APRENDIZADO" ):
			$arr = setAprendizado(array(
				"frm" => $frm,
				"id" => $barDecode["id"],
				"fi" => $barDecode["fi"],
				"ni" => $barDecode["ni"]
			));
			
		//ACOMPANHAMENTO
		elseif ( $op == "ACOMPANHAMENTO" ):
			$arr = getAcompanhamento(array(
				"it" => $barDecode["fi"],
				"ip" => $barDecode["ni"]
			));
			
		//READ BARCODE ONLY
		elseif ( $op == "READBC" ):
			$arr = array(
				"bf" => $barDecode["id"],
				"ia" => $barDecode["fi"],
				"ip" => $barDecode["ni"],
				"logged" => true
			);
			
		endif;
		
	endif;
	return $arr;
}

function setAprendizado( $parm ){
	$arrRetorno = array();
	$arr['logged'] = false;
	$arr['result'] = "Item inválido!";
	
	if ( $parm["fi"] > 0 ):
		$paramDates = getParamDates( $parm["frm"] );
		
		//A-CARTAO
		if ($parm["id"] == 10):
			$arrRetorno = updateHistorico( $parm["ni"], $parm["fi"], $paramDates );
			$str = $arrRetorno["ap"] ."<br/>". $arrRetorno["nm"];
			
			if ($arrRetorno["ar"] == "REGULAR"):
				$rs = $GLOBALS['conn']->Execute("
					SELECT ID, DS_ITEM
					  FROM TAB_APRENDIZADO
					 WHERE CD_ITEM_INTERNO = '". substr($arrRetorno["cd"],0,-3) ."-01'
				");
				if (!$rs->EOF):
					$barCODE = $GLOBALS['pattern']->getBars()->encode(array(
						"id" => "A",
						"fi" => $rs->fields["ID"],
						"ni" => $parm["ni"]
					));
					$str .= "<br/><a onclick=\"javascript:onscan('$barCODE');\" role=\"button\" class=\"btn btn-warning pull-right\">&nbsp;Adicionar&nbsp;".titleCase($rs->fields["DS_ITEM"])."</a>";
				endif;
			endif;
			$arr['result'] = $str;
			$arr['logged'] = true;
		
		//ESPECIALIDADE
		elseif ($parm["id"] == 14):
			$arrRetorno = updateHistorico( $parm["ni"], $parm["fi"], $paramDates );
			$arr['result'] =  $arrRetorno["ap"]." - ".$arrRetorno["cd"]." - #".$arrRetorno["pg"]."<br/>". $arrRetorno["nm"];
			$arr['logged'] = true;
		endif;
	endif;
	return $arr;
}

function setChamada( $parm ) {
	$dhApontamento = date('Y-m-d H:i:s');
	$aParam = Array();

	$sQPadrao = "SELECT rg.id, tr.ds, ev.info_adic 
		  FROM RGR_CHAMADA rg
	INNER JOIN TAB_RGR_CHAMADA tr ON (tr.ID = rg.ID_TAB_RGR_CHAMADA)
	INNER JOIN CAD_EVENTOS ev ON (ev.ID_EVENTO = rg.ID_EVENTO)
		 WHERE ev.FLAG_PUBLICACAO = 'S'
		   AND ( ? BETWEEN ev.DTHORA_EVENTO_INI AND ev.DTHORA_EVENTO_FIM )";
	$aParam[0] = $dhApontamento;
	
	//VERIFICA FUNCAO
	if ( isset($parm["id"]) && !empty($parm["id"]) ):
		$sQPadrao .= " AND tr.NR_PADRAO = ?";
		$aParam[] = $parm["id"];
	endif;

	$result = $GLOBALS['conn']->Execute( $sQPadrao, $aParam );

	if (!$result->EOF):
		$dsFuncao = mb_strtoupper($result->fields['ds']);
		$dsEvento = mb_strtoupper($result->fields['info_adic']);
		$idRegra  = $result->fields['id'];
		
		$nmPessoa = "";
		$rs = $GLOBALS['conn']->Execute("SELECT nm FROM CAD_PESSOA WHERE id = ?", Array( $parm["ni"] ) );
		if (!$rs->EOF):
			$nmPessoa = mb_strtoupper($rs->fields['nm']);
		endif;
		
		$dsItem = "";
		//A-CARTAO, B-CADERNO, C-PASTA
		if ($parm["id"] == 10 || $parm["id"] == 11 || $parm["id"] == 12):
			$rs = $GLOBALS['conn']->Execute("SELECT ds_item FROM TAB_APRENDIZADO WHERE id = ?", Array( $parm["fi"] ) );
			if (!$rs->EOF):
				$dsItem = mb_strtoupper($rs->fields['ds_item']);
			endif;
		
		//D-AUTOTIZACAO DE SAIDA COMUM, 15-AUTORIZACAO ESPECIAL
		elseif ($parm["id"] == 13 || $parm["id"] == 15):
			$rs = $GLOBALS['conn']->Execute("SELECT ds FROM CAD_EVENTOS_SAIDA WHERE id = ?", Array( $parm["fi"] ) );
			if (!$rs->EOF):
				$dsItem = mb_strtoupper($rs->fields['ds']);
				$dsEvento = "";
			endif;
		endif;
		
		$rs = $GLOBALS['conn']->Execute("
			SELECT u.cd_usuario, l.dh 
			  FROM LOG_CHAMADA l 
		INNER JOIN CAD_USUARIOS u ON (u.id_usuario = l.id_usuario) 
			 WHERE l.id_rgr_chamada = ? 
			   AND l.id_cad_pessoa = ?", Array( $idRegra, $parm["ni"] ) );
			   
		if (!$rs->EOF):
			$arr['result'] = ("Apontamento j&aacute; realizado por ".mb_strtoupper($rs->fields['cd_usuario'])." em ".strftime("%d/%m/%Y &agrave;s %H:%M:%S", strtotime($rs->fields['dh'])));
		
		else:
			$GLOBALS['conn']->Execute("
				INSERT INTO LOG_CHAMADA (
					id_cad_pessoa, id_rgr_chamada, dh, id_usuario
				) VALUES (
					?,?,?,?
				)", 
			array( $parm["ni"], $idRegra, $dhApontamento, $_SESSION['USER']['id_usuario'] ) );
			
			$strRetorno = "";
			if ( !empty($nmPessoa) ):
				$strRetorno .= "$nmPessoa<br/>";
			endif;
			if ( !empty($dsItem) ):
				$strRetorno .= "$dsItem<br/>";
			endif;
			if ( !empty($dsFuncao) ):
				$strRetorno .= "$dsFuncao<br/>";
			endif;
			if ( !empty($dsEvento) ):
				$strRetorno .= "$dsEvento<br/>";
			endif;
			$strRetorno .= strftime("%d/%m/%Y &agrave;s %H:%M:%S", strtotime($dhApontamento));
			
			$arr['result'] = ($strRetorno);
			$arr['logged'] = true;	
		endif;
		
	else:
		$arr['result'] = "Item de chamada inválido!";
	endif;
	
	return $arr;
}
?>