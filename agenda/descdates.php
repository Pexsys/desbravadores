<?php 
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
		// SE TIVER SE DATA INICIO
		// SE DATA INICIO E FIM SAO IGUAIS
		//******************************************************************
		$Wdata_ini = strtotime(fFormatFromDB($DATA_EVENTO_INI,"MDA"));
		$Wdata_iniD1 = mktime(0, 0, 0, date("m",$Wdata_ini), date("d",$Wdata_ini)+1, date("Y",$Wdata_ini));
		$Wdata_fim = strtotime(fFormatFromDB($DATA_EVENTO_FIM,"MDA"));
		$Wdata_hoje = strtotime(fFormatFromDB($DATA_HOJE,"MDA"));

		if ($DATA_EVENTO_FIM == "" || $DATA_EVENTO_INI == $DATA_EVENTO_FIM):
			if ($DATA_EVENTO_INI == $DATA_HOJE):
				$sDataHora = "Hoje";
			elseif ($DATA_EVENTO_INI == $DATA_D1):
				$sDataHora = "Amanh&atilde;";
			elseif (fDifDatas($DATA_HOJE,$DATA_EVENTO_INI,"D") <= 7):
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
					$sDataHora .= "Amanh&aacute;";
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
				$sDataHora .= " das ";
				$sDataHora .= fDescHora($DTHORA_EVENTO_INI);
			endif;
			$sDataHora .= " &agrave;s ";
			$sDataHora .= fDescHora($DTHORA_EVENTO_FIM);
		else:
			$sDataHora .= " &agrave;s ";
			$sDataHora .= fDescHora($DTHORA_EVENTO_INI);
		endif;

		return utf8_encode($sDataHora);
	}
?>