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
		$barini 	= substr($brdt, 0, 1);
		$barfn		= base_convert( substr($brdt, 1, 1), 36, 10 );
		$barfnid 	= base_convert( substr($brdt, 2, 2), 36, 10 );
		$barpessoaid	= base_convert( substr($brdt, 4, 3), 36, 10 );
		
		fConnDB();
			
		if ( $barini !== "P" || strlen( $brdt ) <> 7 ):
			$arr['result'] = "Código não pertence ao Clube Pioneiros";

		//CHAMADA
		elseif ( $op == "CHAMADA" ):
			$arr = setChamada(array(
				"barini" => $barini,
				"barfn" => $barfn,
				"barfnid" => $barfnid,
				"barpessoaid" => $barpessoaid
			));
			
		//APRENDIZADO
		elseif ( $op == "APRENDIZADO" ):
			$arr = setAprendizado(array(
				"frm" => $frm,
				"barfn" => $barfn,
				"barfnid" => $barfnid,
				"barpessoaid" => $barpessoaid
			));
			
		//ACOMPANHAMENTO
		elseif ( $op == "ACOMPANHAMENTO" ):
			$arr = getAcompanhamento(array(
				"it" => $barfnid,
				"ip" => $barpessoaid
			));
			
		//READ BARCODE ONLY
		elseif ( $op == "READBC" ):
			$arr = array(
				"bf" => $barfn,
				"ia" => $barfnid,
				"ip" => $barpessoaid,
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
	
	if ( $parm["barfnid"] > 0 ):
		$paramDates = getParamDates( $parm["frm"] );
		
		//A-CARTAO
		if ($parm["barfn"] == 10):
			$arrRetorno = updateHistorico( $parm["barpessoaid"], $parm["barfnid"], $paramDates );
			$str = $arrRetorno["ap"] ."<br/>". $arrRetorno["nm"];
			
			if ($arrRetorno["ar"] == "REGULAR"):
				$rs = $GLOBALS['conn']->Execute("
					SELECT ID, DS_ITEM
					  FROM TAB_APRENDIZADO
					 WHERE CD_ITEM_INTERNO = '". substr($arrRetorno["cd"],0,-3) ."-01'
				");
				if (!$rs->EOF):
					$barCODE = mb_strtoupper("PA". fStrZero(base_convert($rs->fields["ID"],10,36),2) . fStrZero(base_convert($parm["barpessoaid"],10,36),3));
					$str .= "<br/><a onclick=\"javascript:onscan('$barCODE');\" role=\"button\" class=\"btn btn-warning pull-right\">&nbsp;Adicionar&nbsp;".titleCase($rs->fields["DS_ITEM"])."</a>";
				endif;
			endif;
			$arr['result'] = $str;
			$arr['logged'] = true;
		
		//ESPECIALIDADE
		elseif ($parm["barfn"] == 14):
			$arrRetorno = updateHistorico( $parm["barpessoaid"], $parm["barfnid"], $paramDates );
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
	if ( isset($parm["barfn"]) && !empty($parm["barfn"]) ):
		$sQPadrao .= " AND tr.NR_PADRAO = ?";
		$aParam[] = $parm["barfn"];
	endif;

	$result = $GLOBALS['conn']->Execute( $sQPadrao, $aParam );

	if (!$result->EOF):
		$dsFuncao = mb_strtoupper($result->fields['ds']);
		$dsEvento = mb_strtoupper($result->fields['info_adic']);
		$idRegra  = $result->fields['id'];
		
		$nmPessoa = "";
		$rs = $GLOBALS['conn']->Execute("SELECT nm FROM CAD_PESSOA WHERE id = ?", Array( $parm["barpessoaid"] ) );
		if (!$rs->EOF):
			$nmPessoa = mb_strtoupper($rs->fields['nm']);
		endif;
		
		$dsItem = "";
		//A-CARTAO, B-CADERNO, C-PASTA
		if ($parm["barfn"] == 10 || $parm["barfn"] == 11 || $parm["barfn"] == 12):
			$rs = $GLOBALS['conn']->Execute("SELECT ds_item FROM TAB_APRENDIZADO WHERE id = ?", Array( $parm["barfnid"] ) );
			if (!$rs->EOF):
				$dsItem = mb_strtoupper($rs->fields['ds_item']);
			endif;
		
		//D-AUTOTIZACAO DE SAIDA COMUM, 15-AUTORIZACAO ESPECIAL
		elseif ($parm["barfn"] == 13 || $parm["barfn"] == 15):
			$rs = $GLOBALS['conn']->Execute("SELECT ds FROM CAD_EVENTOS_SAIDA WHERE id = ?", Array( $parm["barfnid"] ) );
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
			   AND l.id_cad_pessoa = ?", Array( $idRegra, $parm["barpessoaid"] ) );
			   
		if (!$rs->EOF):
			$arr['result'] = ("Apontamento j&aacute; realizado por ".mb_strtoupper($rs->fields['cd_usuario'])." em ".strftime("%d/%m/%Y &agrave;s %H:%M:%S", strtotime($rs->fields['dh'])));
		
		else:
			$GLOBALS['conn']->Execute("
				INSERT INTO LOG_CHAMADA (
					id_cad_pessoa, id_rgr_chamada, dh, id_usuario
				) VALUES (
					?,?,?,?
				)", 
			array( $parm["barpessoaid"], $idRegra, $dhApontamento, $_SESSION['USER']['id_usuario'] ) );
			
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