<style type="text/css">
<!--
.style1 {
	font-size: 12px
}

.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
}

.style6 {
	color: #FFFFFF
}
-->
</style>
<br>
<br>
<table width="100%" border="0" cellpadding="5" cellspacing="1"
	valign="top">
	<tr>
		<td style="font-family: Arial; font-size: 9px;"
			onclick="javascript:window.location.replace('<?php echo $o->PHPDomain . $VirtualDir?>agenda/index.php');"
			onmouseover="javascript:this.style.cursor='hand';"
			onmouseout="javascript:this.style.cursor='default';"><span
			class="style1">Sair da Agenda</span></td>
		<td style="font-family: Arial; font-size: 9px;"
			onclick="javascript:window.location.replace('<?php echo $o->PHPDomain . $VirtualDir?>agenda/agenda_manut.php');"
			onmouseover="javascript:this.style.cursor='hand';"
			onmouseout="javascript:this.style.cursor='default';"><span
			class="style1">Atualizar</span></td>
		<td style="font-family: Arial; font-size: 9px;"
			onclick="javascript:window.location.replace('<?php echo $o->PHPDomain . $VirtualDir?>agenda/agenda_manut.php?ACAO=NEW');"
			onmouseover="javascript:this.style.cursor='hand';"
			onmouseout="javascript:this.style.cursor='default';"><span
			class="style1">Inserir Novo Evento</span></td>
	</tr>
</table>