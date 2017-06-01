<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Agenda</title>
<meta http-equiv="Pragma" content="no-cache">
<meta HTTP-EQUIV="Pragma-directive" CONTENT="no-cache">
<meta HTTP-EQUIV="cache-directive" CONTENT="no-cache">
<meta http-equiv="Cache-Control" content="must-revalidate">
<meta http-equiv="Cache-Control" content="max-age=0">
<meta http-equiv="Expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
<meta http-equiv="Content-Language" content="pt-br">
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}
.style5 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; }
body {
	background-image: url();
	background-color: #F4F4F4;
}
.style6 {color: #FFFFFF}
-->
</style>
</head>
<body>
<?php
	require_once("../include/header_simples.php");
	require_once("../include/_variables.php");
	include("../include/functions.php");
	require_once("phplib.php");
	$o = new clbd;
	$o->fInitServer($_SERVER['SERVER_NAME']);
	$o->dbConecta();

	$ACAO = $o->fRequest("ACAO");
	if ($ACAO == "NEW" || $ACAO == "EDIT"):
		$ID_EVENTO = $o->fRequest("ID_EVENTO") * 1;
		if ($ID_EVENTO > 0):
			$gsComando = "SELECT * FROM CAD_EVENTOS WHERE ID_EVENTO = $ID_EVENTO";
			$total = $o->dbQuery($gsComando,0);
			if (($row = $o->dbFetch(0))):
				$INIEVE_DATA = $o->fFormatBancoData(substr($row->DTHORA_EVENTO_INI,0,10));
				$INIEVE_HORA = substr($row->DTHORA_EVENTO_INI,11,5);

				$FIMEVE_DATA = $o->fFormatBancoData(substr($row->DTHORA_EVENTO_FIM,0,10));
				$FIMEVE_HORA = substr($row->DTHORA_EVENTO_FIM,11,5);
			endif;
		endif;
		?>
		<p><br>
	     </p>
		<table width="0" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="10">&nbsp;</td>
            <td width="370"><img src="<?php echo $VirtualDir?>images/TopBackGray.jpg"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td bgcolor="#666666"><div align="center" class="style6"><span class="style1"> CADASTRO DE EVENTOS </span></div></td>
          </tr>
        </table>
		<p>&nbsp;</p>
		<form name=frmAgenda id=frmAgenda method="post" action="<?php echo $o->PHPDomain . $VirtualDir?>agenda/agenda_manut.php">
			<table width="100%" border="0" cellpadding="5" cellspacing="1" valign="top">
				<input type=hidden name=ID_EVENTO ID=ID_EVENTO value="<?php echo $ID_EVENTO;?>">
				<input type=hidden name=ACAO ID=ACAO value="GRAVAR">
				<tr>
					<td valign=top><span class="style1"><strong>Data/Hora Início:</strong>                    
				    <input type=text size=11 maxlength=10 name=INIEVE_DATA id=INIEVE_DATA value="<?php echo $INIEVE_DATA;?>">
                    <strong>(dd/mm/yyyy)&nbsp;&nbsp;</strong>&nbsp;&nbsp;
                    <input type=text size=6 maxlength=5 name=INIEVE_HORA id=INIEVE_HORA value="<?php echo $INIEVE_HORA;?>"> 
                    (hh:mm)</span></td>
				</tr>
				<tr>
				  <td valign=top><span class="style5">Data/Hora Final:</span>					  <input type=text size=11 maxlength=10 name=FIMEVE_DATA id=FIMEVE_DATA value="<?php echo $FIMEVE_DATA;?>"> <span class="style1"><strong>(dd/mm/yyyy)&nbsp;&nbsp;</strong>&nbsp;&nbsp;
			      <input type=text size=6 maxlength=5 name=FIMEVE_HORA id=FIMEVE_HORA value="<?php echo $FIMEVE_HORA;?>"> 
			      <strong>(hh:mm)</strong></span></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>Local:</strong>				    
				    <input type=text size=61 maxlength=60 name=DESC_LOCAL id=DESC_LOCAL value="<?php echo $row->DESC_LOCAL;?>">
					</span></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>Logradouro:</strong>				    
				    <input type=text size=61 maxlength=60 name=DESC_LOGRADOURO id=DESC_LOGRADOURO value="<?php echo $row->DESC_LOGRADOURO;?>">
					</span></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>Número:</strong>                    
				    <input type=text size=6 maxlength=5 name=NUM_LOGRADOURO id=NUM_LOGRADOURO value="<?php echo $row->NUM_LOGRADOURO;?>">
					</span></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>Complemento:</strong>				    
				    <input type=text size=31 maxlength=30 name=DESC_COMPLEMENTO id=DESC_COMPLEMENTO value="<?php echo $row->DESC_COMPLEMENTO;?>">
					</span></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>Bairro:</strong>				    
				    <input type=text size=31 maxlength=30 name=DESC_BAIRRO id=DESC_BAIRRO value="<?php echo $row->DESC_BAIRRO;?>">
					</span></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>Cidade:</strong>				    
				    <input type=text size=31 maxlength=30 name=DESC_CIDADE id=DESC_CIDADE value="<?php echo $row->DESC_CIDADE;?>">
					</span></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>UF:</strong>				    
				    <input type=text size=3 maxlength=2 name=COD_UF id=COD_UF value="<?php echo $row->COD_UF;?>">
					</span></td>
				</tr>
				<tr>
					<td valign=top><span class="style5">Informações Adicionais:</span><br>					  <textarea name=INFO_ADIC id=INFO_ADIC cols=100 rows=10><?php echo $row->INFO_ADIC;?></TEXTAREA></td>
				</tr>
				<tr>
					<td valign=top><span class="style1"><strong>Confirmado:</strong>				    
				    <input type=text size=2 maxlength=1 name=FLAG_PUBLICACAO id=FLAG_PUBLICACAO value="<?php echo $row->FLAG_PUBLICACAO;?>"> 
				    <strong>(S ou N)</strong></span></td>
				</tr>
				<tr>
					<td valign=top>
						<input type=button value="Voltar para a Lista" onclick="javascript:window.location.replace('agenda_manut.php');">    
						<input type=button value="Gravar Evento" onclick="javascript:fConsisteDados();">    
					</td>
				</tr>
			</table>
			<script language=javascript>
				function fConsisteDados(){
					try{
						var msgErro = '';
						if (document.frmAgenda.INIEVE_DATA.value.length != 10){
							msgErro = 'Data Incompleta!';
						}else if(document.frmAgenda.INIEVE_HORA.value.length != 5){
							msgErro = 'Hora Incompleta!';
						}
						if (msgErro != ''){
							alert(msgErro);
						}else{
							document.frmAgenda.submit();
						}
					}catch(err){
						alert('Erro nos Dados!');
					}
				}
			</script>
		</form>
		<?php 
	else:
		if ($ACAO == "GRAVAR" || $ACAO == "DELETE"):
			$ID_EVENTO = $o->fRequest("ID_EVENTO") * 1;
			$DTHORA_EVENTO_INI = $o->fFormatDataBanco($o->fRequest("INIEVE_DATA") . " " . $o->fRequest("INIEVE_HORA") . ":00");
			$DTHORA_EVENTO_FIM = $o->fFormatDataBanco($o->fRequest("FIMEVE_DATA") . " " . $o->fRequest("FIMEVE_HORA") . ":00");
			if (strlen($DTHORA_EVENTO_FIM) < 19):
				$DTHORA_EVENTO_FIM = "NULL";
			else:
				$DTHORA_EVENTO_FIM = "'$DTHORA_EVENTO_FIM'";
			endif;

			$DESC_LOCAL = $o->fRequest("DESC_LOCAL");
			$DESC_LOGRADOURO = $o->fRequest("DESC_LOGRADOURO");
			$NUM_LOGRADOURO = $o->fRequest("NUM_LOGRADOURO");
			$DESC_COMPLEMENTO = $o->fRequest("DESC_COMPLEMENTO");
			$DESC_BAIRRO = $o->fRequest("DESC_BAIRRO");
			$DESC_CIDADE = $o->fRequest("DESC_CIDADE");
			$COD_UF = $o->fRequest("COD_UF");
			$INFO_ADIC = $o->fRequest("INFO_ADIC");
			$FLAG_PUBLICACAO = mb_strtoupper($o->fRequest("FLAG_PUBLICACAO"));

			if ($ID_EVENTO == 0):
				$gsComando = "SELECT MAX(ID_EVENTO) + 1 AS MAX_ID_EVENTO FROM CAD_EVENTOS";
				$total = $o->dbQuery($gsComando,0);
				if ($row = $o->dbFetch(0)):
					$ID_EVENTO = $row->MAX_ID_EVENTO * 1;
					if ($ID_EVENTO == 0):
						$ID_EVENTO = 1;
					endif;
					$gsComando = "INSERT INTO CAD_EVENTOS(ID_EVENTO, DTHORA_EVENTO_INI, DTHORA_EVENTO_FIM, DESC_LOCAL, DESC_LOGRADOURO, NUM_LOGRADOURO, DESC_COMPLEMENTO, DESC_BAIRRO, DESC_CIDADE, COD_UF, INFO_ADIC, DTHORA_INCLUSAO, FLAG_PUBLICACAO) ";
					$gsComando .= " VALUES($ID_EVENTO, '$DTHORA_EVENTO_INI', $DTHORA_EVENTO_FIM, '$DESC_LOCAL', '$DESC_LOGRADOURO', '$NUM_LOGRADOURO', '$DESC_COMPLEMENTO', '$DESC_BAIRRO', '$DESC_CIDADE', '$COD_UF', '$INFO_ADIC', '". date('Y-m-d H:i:s') ."', '$FLAG_PUBLICACAO')";
				endif;
			elseif ($ACAO == "DELETE"):
				$gsComando = "DELETE FROM CAD_EVENTOS WHERE ID_EVENTO = $ID_EVENTO";
			else:
				$gsComando = "UPDATE CAD_EVENTOS SET ";
				$gsComando .= "			DTHORA_EVENTO_INI = '$DTHORA_EVENTO_INI', ";
				$gsComando .= "			DTHORA_EVENTO_FIM = $DTHORA_EVENTO_FIM, ";
				$gsComando .= "			DESC_LOCAL = '$DESC_LOCAL', ";
				$gsComando .= "			DESC_LOGRADOURO = '$DESC_LOGRADOURO', ";
				$gsComando .= "			NUM_LOGRADOURO = '$NUM_LOGRADOURO', ";
				$gsComando .= "			DESC_COMPLEMENTO = '$DESC_COMPLEMENTO', ";
				$gsComando .= "			DESC_BAIRRO = '$DESC_BAIRRO', ";
				$gsComando .= "			DESC_CIDADE = '$DESC_CIDADE', ";
				$gsComando .= "			COD_UF = '$COD_UF', ";
				$gsComando .= "			INFO_ADIC = '$INFO_ADIC', ";
				$gsComando .= "			FLAG_PUBLICACAO = '$FLAG_PUBLICACAO' ";
				$gsComando .= "	WHERE ID_EVENTO = $ID_EVENTO";
			endif;
			$total = $o->dbQuery($gsComando,0);
		endif;

		$DATA_NOW = date('Y-m-d H:i:s');
		$query = "SELECT ";
		$query .= "   * ";
		$query .= " FROM ";
		$query .= "		CAD_EVENTOS ";
		$query .= " WHERE ";
		$query .= "				( DTHORA_EVENTO_INI >= '$DATA_NOW' OR ( DTHORA_EVENTO_FIM IS NOT NULL AND DTHORA_EVENTO_FIM >= '$DATA_NOW' ) )";
		$query .= "				";
		$query .= " ORDER BY ";
		$query .= "		DTHORA_EVENTO_INI ";
		$total = $o->dbQuery($query,0);
		$MES_ANT = "";
		if (($row = $o->dbFetch(0))):
			do {
				$MES_ATU = $o->fDescMes($o->fFormatBancoData($row->DTHORA_EVENTO_INI));
				$cHora = $o->fDescHora(substr($row->DTHORA_EVENTO_INI,11,8));
				$cDia = $o->fStrZero(substr($row->DTHORA_EVENTO_INI,8,2),2);
				
				if ($MES_ATU != $MES_ANT):
					if ($MES_ANT != ""):
						?></table></td></tr></table><?php 
					endif;
					$MES_ANT = $MES_ATU;

					include("agenda_menu.php");
					?>
					<table width=100% border=0 cellpadding=0 cellspacing=0 valign=top>
						<tr bgcolor=#AAAAAA>
							<td width=20px><img src="<?php echo $o->PHPDomain . $VirtualDir?>agenda/min.png" onclick="javascript:trocaImagem(this);" border=0 WIDTH=11 HEIGHT=11 style="cursor:hand;"></td>
							<td width=100%><font color=#990000 size=2 face="Arial"><b><?php echo $MES_ANT;?></b></font></td>
						</tr>
						<tr>
							<td colspan="2">
								<table width="100%" border="0" cellpadding="5" cellspacing="1" valign="top">
									<tr bgcolor="#CCCCCC">
										<td><font color="#000000" size="2" face="Arial"> </font></td>
										<td><font color="#000000" size="2" face="Arial"> </font></td>
										<td width="15%"><font color="#000000" size="2" face="Arial">Dia</font></td>
										<td width="7%" align=center><font color="#000000" size="2" face="Arial">Hora</font></td>
										<td width="72%"><font color="#000000" size="2" face="Arial">Local do Evento</font></td>
										<td width="6%"><font color="#000000" size="2" face="Arial">Confirmado</font></td>
									</tr>
					<?php 
				endif;
				
				$strExclusao = str_replace("\r\n","<br>",str_replace("'","´",str_replace("\"","´",$row->DESC_LOCAL))) . " " . $cHora;
				?>
				<tr bgcolor="#F0F0F0" onmouseover="javascript:this.style.backgroundColor='#E4E4E4';" onmouseout="javascript:this.style.backgroundColor='#F0F0F0';">
					<td onmouseover="javascript:this.style.cursor='hand';" onmouseout="javascript:this.style.cursor='default';" onclick="javascript:window.location.replace('agenda_manut.php?ACAO=EDIT&ID_EVENTO=<?php echo $row->ID_EVENTO;?>');"><img src="<?php echo $o->PHPDomain . $VirtualDir?>agenda/edit.png" border=0 alt="Editar"></td>
					<td onmouseover="javascript:this.style.cursor='hand';" onmouseout="javascript:this.style.cursor='default';" onclick="javascript:fExcluir(<?php echo $row->ID_EVENTO;?>,'<?php echo $strExclusao;?>');"><img src="<?php echo $o->PHPDomain . $VirtualDir?>agenda/delete.png" border=0 alt="Apagar"></td>
					<td width="15%"><font color="#000000" size="2" face="Arial"><?php echo $o->fNDiaDSemana($row->DTHORA_EVENTO_INI,false,true);?></font></td>
					<td width="7%" align=center><font color="#000000" size="2" face="Arial"><?php echo $cHora;?></font></td>
					<td width="55%"><font color="#000000" size="2" face="Arial"><?php echo str_replace("\r\n","<br>",$row->DESC_LOCAL);?></font></td>
					<td width="6%"><font color="#000000" size="2" face="Arial"><?php echo $row->FLAG_PUBLICACAO;?></font></td>
				</tr>
				<?php 
			} while ($row = $o->dbFetch(0));
		endif;
		?>
		</table></td></tr></table>
		<?php include("agenda_menu.php")?>
		<script language=javascript>
			function trocaImagem(objImg){
				var sourceImg = objImg.src;
				var tblprincipal = objImg.parentElement.parentElement.parentElement.parentElement;
				var tbldetalhe = tblprincipal.rows(1).cells(0).children(0);
				if(sourceImg.search("min") != -1){
					objImg.src = '<?php echo $o->PHPDomain . $VirtualDir?>agenda/add.png';
					tbldetalhe.style.display = 'none';
				}else{
					objImg.src = '<?php echo $o->PHPDomain . $VirtualDir?>agenda/min.png';
					tbldetalhe.style.display = '';
				}
			}

			function fExcluir(ID_EVENTO,STR){
				if (confirm(STR+'\n\nDeseja Realmente Excluir o evento?\n\nOK - Excluir\nCancelar - Voltar')){
					window.location.replace('<?php echo $o->PHPDomain . $VirtualDir?>agenda/agenda_manut.php?ACAO=DELETE&ID_EVENTO='+ID_EVENTO);
				}
			}
		</script>
		<?php 
	endif;
?>
</font>
</body>
</html>