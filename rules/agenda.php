<?php
@require_once("../include/functions.php");
responseMethod();

/****************************
 * Methods defined for use. *
 ****************************/
function events( $parameters ) {
	$DATA_NOW = date('Y-m-d H:i:s');
	
	$out = array();
	fConnDB();
	$aBind = array();

	$query = "SELECT e.*, 
					 c.TP_GRUPO, c.ID_TAB_RGR_CHAMADA, c.ID_TAB_TP_UNIFORME,
					 r.DS as DS_RGR_CHAMADA,
					 u.DS as DS_TP_UNIFORME
		    FROM CAD_EVENTOS e
	       LEFT JOIN RGR_CHAMADA c ON (c.ID_EVENTO = e.ID_EVENTO)
		   LEFT JOIN TAB_RGR_CHAMADA r ON (r.ID = c.ID_TAB_RGR_CHAMADA)
		   LEFT JOIN TAB_TP_UNIFORME u ON (u.ID = c.ID_TAB_TP_UNIFORME)";

	if ( isset($parameters["id"]) ):
		$query .= " WHERE e.ID_EVENTO = ?";
		$aBind = array($parameters["id"]);
	else:
		$query .= " WHERE e.DTHORA_EVENTO_INI >= ?
			     AND e.DTHORA_EVENTO_FIM <= ?
			ORDER BY e.DTHORA_EVENTO_INI ";
		$aBind = array( $parameters["from"], $parameters["to"] );
	endif;
	$result = $GLOBALS['conn']->Execute( $query, $aBind );
	
	while (!$result->EOF):
	
		$dh_ini = $result->fields['DTHORA_EVENTO_INI'];
		$dh_fim = $result->fields['DTHORA_EVENTO_FIM'];
	
		$dt_hora_eve = fDtHoraEvento( $dh_ini, $dh_fim );
		
		$ds_info_add = trim($result->fields['INFO_ADIC']);
		
		$title = $dt_hora_eve;
		$title = fConcatNoEmpty($title, " - ", $ds_info_add);
		if (!is_null($result->fields['TP_GRUPO'])):
			$title .= " - G:".$result->fields['TP_GRUPO'];
		endif;
		if (!is_null($result->fields['ID_TAB_RGR_CHAMADA'])):
			$title .= " - R:".$result->fields['DS_RGR_CHAMADA'];
		endif;
		if (!is_null($result->fields['ID_TAB_TP_UNIFORME'])):
			$title .= " - U:".$result->fields['DS_TP_UNIFORME'];
		endif;
		if (!is_null($result->fields['FG_INSTRUCAO'])):
			$title .= " - I:".$result->fields['FG_INSTRUCAO'];
		endif;
		
		$tipo_evento = trim($result->fields['TIPO_EVENTO']);

		$out[] = array(
			'id' => $result->fields['ID_EVENTO'],
			'title' => utf8_encode($title),
			'url' => '',
			'class' => ( $dh_fim < $DATA_NOW ? '' : fGetClass($tipo_evento) ),
			'info' => 
				 array(
					"id"		=> $result->fields['ID_EVENTO'],
					"dh_ini"	=> strtotime($dh_ini) .'000',
					"dh_fim"	=> strtotime($dh_fim) .'000',
					"ds_info"	=> utf8_encode($ds_info_add),
					"ds_local"	=> utf8_encode(trim($result->fields['DESC_LOCAL'])),
					"ds_logra"	=> utf8_encode(trim($result->fields['DESC_LOGRADOURO'])),
					"nr_logra"	=> utf8_encode(trim($result->fields['NUM_LOGRADOURO'])),
					"ds_cmpl"	=> utf8_encode(trim($result->fields['DESC_COMPLEMENTO'])),
					"ds_bai"	=> utf8_encode(trim($result->fields['DESC_BAIRRO'])),
					"ds_cid"	=> utf8_encode(trim($result->fields['DESC_CIDADE'])),
					"cd_uf"		=> $result->fields['COD_UF'],
					"fg_publ"	=> $result->fields['FLAG_PUBLICACAO'],
					"tp_eve"	=> $tipo_evento,
					"tp_grupo"	=> $result->fields['TP_GRUPO'],
					"id_regra"	=> $result->fields['ID_TAB_RGR_CHAMADA'],
					"id_uniforme"	=> $result->fields['ID_TAB_TP_UNIFORME'],
					"fg_instrucao"	=> $result->fields['FG_INSTRUCAO']	
				),
			'start' => strtotime($dh_ini) .'000',
			'end' => strtotime($dh_fim) .'000'
		);
		$result->MoveNext();
	endwhile;
	return array( 'success' => 1, 'result' => $out );
}

function fEvent( $parameters ) {
	$out = array();
	$out["success"] = false;

	fConnDB();

	$frm = $parameters["frm"];
	$op = $parameters["op"];
	$id = 0;

	//LEITURA DE EVENTO.
	//ATUALIZACAO DE EVENTO
	if ( $op == "UPDATE" ):
		
		$arr = array();
		//INSERT DE NOVO EVENTO
		if ( !is_null($frm["id"]) && is_numeric($frm["id"]) ):
			$id = $frm["id"];
			$query = "
			UPDATE CAD_EVENTOS SET
				DTHORA_EVENTO_INI = ?,
				DTHORA_EVENTO_FIM = ?,
				DESC_LOCAL = ?,	
				DESC_LOGRADOURO = ?,
				NUM_LOGRADOURO = ?,
				DESC_COMPLEMENTO = ?,
				DESC_BAIRRO = ?,
				DESC_CIDADE = ?,
				COD_UF = ?,
				INFO_ADIC = ?,
				TIPO_EVENTO = ?,
				FG_INSTRUCAO = ?,
				FLAG_PUBLICACAO = ?
			WHERE ID_EVENTO = ? ";
			$arr = array(
				fStrToDate($frm["dh_ini"]),
				fStrToDate($frm["dh_fim"]),
				fReturnStringNull($frm["ds_local"]),
				fReturnStringNull($frm["ds_logra"]),
				fReturnStringNull($frm["nr_logra"]),
				fReturnStringNull($frm["ds_cmpl"]),
				fReturnStringNull($frm["ds_bai"]),
				fReturnStringNull($frm["ds_cid"]),
				fReturnStringNull($frm["cd_uf"]),
				fReturnStringNull($frm["ds_info"]),
				fReturnStringNull($frm["tp_eve"]),
				fReturnStringNull($frm["fg_instrucao"]),
				fReturnStringNull($frm["fg_publ"]),
				$id
			);
			$GLOBALS['conn']->Execute($query,$arr);
			
		else:
			$query = "
			INSERT INTO CAD_EVENTOS (
				DTHORA_EVENTO_INI,
				DTHORA_EVENTO_FIM,
				DESC_LOCAL,	
				DESC_LOGRADOURO,
				NUM_LOGRADOURO,
				DESC_COMPLEMENTO,
				DESC_BAIRRO,
				DESC_CIDADE,
				COD_UF,
				INFO_ADIC,
				TIPO_EVENTO,
				FG_INSTRUCAO,
				FLAG_PUBLICACAO
			) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$arr = array(
				fStrToDate($frm["dh_ini"]),
				fStrToDate($frm["dh_fim"]),
				fReturnStringNull($frm["ds_local"]),
				fReturnStringNull($frm["ds_logra"]),
				fReturnStringNull($frm["nr_logra"]),
				fReturnStringNull($frm["ds_cmpl"]),
				fReturnStringNull($frm["ds_bai"]),
				fReturnStringNull($frm["ds_cid"]),
				fReturnStringNull($frm["cd_uf"]),
				fReturnStringNull($frm["ds_info"]),
				fReturnStringNull($frm["tp_eve"]),
				fReturnStringNull($frm["fg_instrucao"],"N"),
				fReturnStringNull($frm["fg_publ"])
			);
			$out["arr"] = $arr;
			
			$GLOBALS['conn']->Execute($query,$arr);
			$id = $GLOBALS['conn']->Insert_ID();
		endif;

		//VERIFICA REGRA CHAMADA.
		$rgr = $GLOBALS['conn']->Execute("SELECT * FROM RGR_CHAMADA WHERE ID_EVENTO = ?", Array( $id ) );
		
		//SE NAO EXISTE NO BANCO E TELA PREENCHIDA.
		if ( $rgr->EOF && ( is_numeric($frm["id_regra"]) || !empty($frm["tp_grupo"]) || is_numeric($frm["id_uniforme"]) ) ):
			$GLOBALS['conn']->Execute("
			INSERT INTO RGR_CHAMADA ( 
				TP_GRUPO, 
				ID_TAB_RGR_CHAMADA, 
				ID_TAB_TP_UNIFORME, 
				ID_EVENTO 
			) VALUES ( ?, ?, ?, ? )", 
				Array( fReturnStringNull($frm["tp_grupo"]),  fReturnNumberNull($frm["id_regra"]), fReturnNumberNull($frm["id_uniforme"]), $id ) );
			
		//SE EXISTE NO BANCO E TELA PREENCHIDA
		elseif ( !$rgr->EOF && ( is_numeric($frm["id_regra"]) || !empty($frm["tp_grupo"]) || is_numeric($frm["id_uniforme"]) ) ):
			$GLOBALS['conn']->Execute("
			UPDATE RGR_CHAMADA SET 
				TP_GRUPO = ?, 
				ID_TAB_RGR_CHAMADA = ?, 
				ID_TAB_TP_UNIFORME = ?
			WHERE ID_EVENTO = ?", 
				Array( fReturnStringNull($frm["tp_grupo"]), fReturnNumberNull($frm["id_regra"]), fReturnNumberNull($frm["id_uniforme"]), $id ) );

		//SE EXISTE NO BANCO E TELA NAO PREENCHIDA
		elseif ( !$rgr->EOF && !( is_numeric($frm["id_regra"]) || !empty($frm["tp_grupo"]) || is_numeric($frm["id_uniforme"]) ) ):
			fDeleteRegraChamada($id);
		endif;
		$out["success"] = true;

	//EXCLUSAO DE EVENTO
	elseif ( $op == "DELETE" ):
		fDeleteRegraChamada($parameters["id"]);
		$GLOBALS['conn']->Execute("DELETE FROM CAD_EVENTOS WHERE ID_EVENTO = ?", Array( $parameters["id"] ) );
		$out["success"] = true;
	
	endif;

	return $out;
}

function fDeleteRegraChamada($idEvento){
	$GLOBALS['conn']->Execute("DELETE FROM RGR_CHAMADA WHERE ID_EVENTO = ?", Array( $idEvento ) );
}

function fGetClass($strTipoEvento){
	$eventClass = array(
		"APS"		=> "event-important",	//VERMELHO
		"IASD"		=> "event-info",	//AZUL
		"REGIAO"	=> "event-success",	//VERDE
		"DEFAULT"	=> "event-inverse",	//PRETO
		"EGW"		=> "event-warning",	//AMARELO
		"SPECIAL"	=> "event-special"	//VINHO
	);
	if (array_key_exists($strTipoEvento,$eventClass)):
		return $eventClass[$strTipoEvento];
	else:
		return fGetClass("DEFAULT");
	endif;
}

function fDtHoraEvento($DTHORA_EVENTO_INI, $DTHORA_EVENTO_FIM){
	$DATA_NOW = date('Y-m-d H:i:s');
	$DATA_D1 = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
	$DATA_HOJE = substr($DATA_NOW,0,10);
	$HORA_NOW = substr($DATA_NOW,11,8);
	$DATA_EVENTO_INI = substr(trim($DTHORA_EVENTO_INI),0,10);
	$HORA_EVENTO_INI = substr(trim($DTHORA_EVENTO_INI),11,8);
	$DATA_EVENTO_FIM = substr(trim($DTHORA_EVENTO_FIM),0,10);
	$HORA_EVENTO_FIM = substr(trim($DTHORA_EVENTO_FIM),11,8);

	$sDataHora = "";

	//******************************************************************
	// SE TIVER SO DATA INICIO
	// SE DATA INICIO E FIM SAO IGUAIS
	//******************************************************************
	$Wdata_ini = strtotime(fFormatFromDB($DATA_EVENTO_INI,"MDA"));
	$Wdata_iniD1 = mktime(0, 0, 0, date("m",$Wdata_ini), date("d",$Wdata_ini)+1, date("Y",$Wdata_ini));
	$Wdata_fim = strtotime(fFormatFromDB($DATA_EVENTO_FIM,"MDA"));
	$Wdata_hoje = strtotime(fFormatFromDB($DATA_HOJE,"MDA"));
	$ndiasDifData = fDifDatas($DATA_HOJE,$DATA_EVENTO_INI,"D");

	if ($DATA_EVENTO_FIM == "" || $DATA_EVENTO_INI == $DATA_EVENTO_FIM):
		if ($DATA_EVENTO_INI == $DATA_HOJE):
			$sDataHora = "Hoje";
		elseif ($DATA_EVENTO_INI == $DATA_D1):
			$sDataHora = "Amanh&atilde;";
		elseif ($ndiasDifData >= 0 && $ndiasDifData <= 7):
			$DIA_SEMANA = strftime("%w",strtotime(fFormatFromDB($DATA_EVENTO_INI,"MDA")));
			if ($DIA_SEMANA == 0):
				$sDataHora = "Pr&oacute;ximo Domingo";
			elseif ($DIA_SEMANA == 1):
				$sDataHora = "Pr&oacute;xima Segunda";
			elseif ($DIA_SEMANA == 2):
				$sDataHora = "Pr&oacute;xima Ter&ccedil;a";
			elseif ($DIA_SEMANA == 3):
				$sDataHora = "Pr&oacute;xima Quarta";
			elseif ($DIA_SEMANA == 4):
				$sDataHora = "Pr&oacute;xima Quinta";
			elseif ($DIA_SEMANA == 5):
				$sDataHora = "Pr&oacute;xima Sexta";
			elseif ($DIA_SEMANA == 6):
				$sDataHora = "Pr&oacute;ximo S&aacute;bado";
			endif;
		else:
			$sDataHora = fFormatFromDB($DATA_EVENTO_INI,"DMr");
		endif;

	//******************************************************************
	// SE DATAS INICIO E FIM SAO DIFERENTES
	//******************************************************************
	else:
		if ($Wdata_hoje >= $Wdata_ini && $Wdata_hoje <= $Wdata_fim):
			if ($HORA_NOW <= $HORA_EVENTO_INI || $HORA_NOW <= $HORA_EVENTO_FIM):
				$sDataHora .= "Hoje";
			elseif ($HORA_NOW >= $HORA_EVENTO_FIM):
				$sDataHora .= "Amanh&atilde;";
			endif;
		else:
			//Dentro do mes
			if (substr($DATA_EVENTO_INI,5,2) == substr($DATA_EVENTO_FIM,5,2)):
				$sDataHora .= substr($DATA_EVENTO_INI,8,2);
			else:
				$sDataHora .= fFormatFromDB($DATA_EVENTO_INI,"DMr");
			endif;
			//se dia consecutivo
			if ($Wdata_iniD1 == $Wdata_fim):
				$sDataHora .= " e ";
			else:
				$sDataHora .= " a ";
			endif;
			$sDataHora .= fFormatFromDB($DATA_EVENTO_FIM,"DMr");
		endif;
	endif;

	//******************************************************************
	// SE O HORARIO FOR DIFERENTE ENTRE AS DATAS  
	//******************************************************************
	if ($HORA_EVENTO_INI != $HORA_EVENTO_FIM && $DATA_EVENTO_FIM != "" && $HORA_EVENTO_FIM != ""):
		if ($Wdata_hoje >= $Wdata_ini && $Wdata_hoje <= $Wdata_fim && $HORA_NOW >= $HORA_EVENTO_INI && $HORA_NOW <= $HORA_EVENTO_FIM):
			$sDataHora .= " at&eacute; ";
		else:
			$sDataHora = fConcatNoEmpty($sDataHora, " das ", fDescHora($DTHORA_EVENTO_INI) );
		endif;
		$sDataHora = fConcatNoEmpty($sDataHora, " &agrave;s ", fDescHora($DTHORA_EVENTO_FIM) );
	else:
		$sDataHora = fConcatNoEmpty($sDataHora, " &agrave;s ", fDescHora($DTHORA_EVENTO_INI) );
	endif;

	return utf8_encode($sDataHora);
}

function agendaConsulta( $parameters ) {
	session_start();
	$membroID = $_SESSION['USER']['id_cad_pessoa'];

	$out = array();
	fConnDB();

	$ano = $parameters["ano"];
	if ( empty($ano) ):
		$out["years"] = array();
		$query = "SELECT DISTINCT NR_ANO 
			  FROM CAD_ATIVOS ". ( is_null($membroID) ? "" : "WHERE ID = $membroID" ) ." ORDER BY NR_ANO DESC";
		$result = $GLOBALS['conn']->Execute($query);
		$ano = $result->fields["NR_ANO"];
		foreach ($result as $k => $line):
			$out["years"][] = array( "id" => $line["NR_ANO"], "ds" => $line["NR_ANO"] );
		endforeach;
	endif;
	if ( !empty($ano) ):
		$mesHoje = date("m");
		$str = "";
		$result = $GLOBALS['conn']->Execute("
		 	SELECT * 
		 	  FROM CAD_EVENTOS 
		 	 WHERE YEAR(DTHORA_EVENTO_INI) = ? 
		 	    OR YEAR(DTHORA_EVENTO_FIM) = ?
		      ORDER BY DTHORA_EVENTO_INI DESC
		", array($ano,$ano) );
		$mesAnt = "<div class=\"row\">";
		foreach ($result as $k => $line):
			$data = strtotime($line['DTHORA_EVENTO_INI']);
			$nomeMesAtu = utf8_encode(ucfirst(strftime("%B",$data)));
			if ($nomeMesAtu != $mesAnt):
				if ($mesAnt != ""):
					$str .= "</div>";
					$str .= "</div>";
					$str .= "</div>";
				endif;
				$mesAnt = $nomeMesAtu;

				$nrMesAtu = strftime("%m",$data);
				$class = "panel-default";
				if (strftime("%Y",$data) == date("Y")):
					if ($nrMesAtu == $mesHoje):
						$class = "panel-red";
					elseif ($nrMesAtu == ($mesHoje + 1)):
						$class = "panel-yellow";
					elseif ($nrMesAtu > $mesHoje):
						$class = "panel-info";
					endif;
				endif;
				$str .= "<div class=\"col-md-6 col-xs-12 col-sm-6 col-lg-4\">";
				$str .= "<div class=\"panel $class\">";
				$str .= "<div class=\"panel-heading\" style=\"cursor:pointer\"><h5 class=\"panel-title\" data-toggle=\"collapse\" data-target=\"#m$nrMesAtu\" href=\"#m$nrMesAtu\"><b>$nomeMesAtu</h5></b></div>";
				$str .= "<div id=\"m$nrMesAtu\" class=\"panel-body panel-collapse collapse\">";
			endif;

			$str .= "<div class=\"media row col-lg-12\">";
			$str .= "<div class=\"pull-left\"><i class=\"fa ". fGetClassTipoEvento($result->fields['TIPO_EVENTO']) ." fa-2x\"></i></div>";
			$str .= "<div class=\"media-body\">";
			$str .= "<h4 class=\"media-heading\"><b>".fDtHoraEvento($result->fields['DTHORA_EVENTO_INI'],$result->fields['DTHORA_EVENTO_FIM'])."</b></h4>";
			$str .= "<p>";
			$info = "";
			if (trim($result->fields['INFO_ADIC']) != ""):
				$info .= trim($result->fields['INFO_ADIC']);
			endif;
			if (trim($result->fields['DESC_LOCAL']) != ""):
				$info .= " - ".trim($result->fields['DESC_LOCAL']);
			endif;
			if ($info != ""):
				$str .= utf8_encode($info) . "<br/>";
			endif;
			$endereco = trim($result->fields['DESC_LOGRADOURO']);
			if (trim($result->fields['NUM_LOGRADOURO']) != ""):
				$endereco .= ", ".trim($result->fields['NUM_LOGRADOURO']);
			endif;
			if (trim($result->fields['DESC_COMPLEMENTO']) != ""):
				$endereco .= " - ".trim($result->fields['DESC_COMPLEMENTO']);
			endif;
			if ($endereco != ""):
				$str .= utf8_encode($endereco) . "<br/>";
			endif;
			$cidade = "";
			if (trim($result->fields['DESC_BAIRRO']) != ""):
				$cidade .= trim($result->fields['DESC_BAIRRO']);
			endif;
			if (trim($result->fields['DESC_CIDADE']) != ""):
				if ($cidade != ""):
					$cidade .= " - ";
				endif;
				$cidade .= trim($result->fields['DESC_CIDADE']);
			endif;
			if (trim($result->fields['COD_UF']) != ""):
				if ($cidade != ""):
					$cidade .= " - ";
				endif;
				$cidade .= trim($result->fields['COD_UF']);
			endif;
			if ($cidade != ""):
				$str .= utf8_encode($cidade)."<br/>";
			endif;
			$str .= "</p>";
			$str .= "</div>";
			$str .= "</div>";
		endforeach;

		$str .= "</div>";
		$str .= "</div>";
		$str .= "</div>";
		$out["agenda"] = $str;
	endif;
	return $out;
}
?>