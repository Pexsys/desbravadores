<?php
if (session_id () == "") :
	session_start ();

	endif;
require_once ("../include/header_simples.php");
require_once ("../include/_variables.php");
include ("../include/functions.php");
require_once ("phplib.php");
$o = new clbd ();
$o->fInitServer ( $_SERVER ['SERVER_NAME'] );
?>
<html>
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=windows-1252">
<title>Autentica&ccedil;&atilde;o Socket</title>
</head>
<body>
<?php
if ($_POST ["pagina"] == "CONEXAO") :
	$user = strtolower ( $_POST ["user"] );
	$pass = $_POST ["pass"];
	
	$lnSock = $o->fLoginUser ( $user, $pass );
	if ($lnSock == - 1) :
		echo "Erro de Conex&atilde;o! Entre em contato com o Administrador (<a href=\"mailto:pexinho@uol.com.br\">pexinho@uol.com.br</a>)!<br>";
	 elseif ($pass == "") :
		echo "Usu&aacute;rio ou senha inv&aacute;lida!<br>";
	 elseif ($lnSock == 1) :
		echo $user;
		?>
		<div align="center">
		<br> <br>
		<table>
			<tr>
				<td><a
					href="<?php echo $o->PHPDomain . $VirtualDir?>agenda/agenda_manut.php">Atualiza&ccedil;&atilde;o
						da Agenda</td>
			</tr>
		</table>
		<p>
			<br> <br>
		        
	<?phpendif;
	echo "<a href=" . $o->PHPDomain . $VirtualDir . "agenda/index.php>Sair</a>";
	exit ();

endif;
session_destroy ();
?>
</p>
		<p>&nbsp;</p>
	</div>
	<form name=frmAtentic id=frmAtentic method=post
		action="<?php echo $o->PHPDomain . $VirtualDir?>agenda/index.php">
		<div align="center">
			<table width="0" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="10">&nbsp;</td>
					<td width="370"><img
						src="<?php echo $VirtualDir?>images/TopBackGray.jpg"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td bgcolor="#666666"><div align="center" class="style6">
							<span class="style1"> CADASTRO DE EVENTOS </span>
						</div></td>
				</tr>
			</table>
			<p>&nbsp;</p>
			<p>
				<input type=hidden name="pagina" id="pagina" value="CONEXAO"> <span
					class="style8">Usu&aacute;rio:</span> <input type=text name=user
					id=user value=""> <span class="style8">@</span><?php echo $o->Domain;?><br>
				<br> <span class="style8">Senha: </span> <input type=password
					name=pass id=pass> <span class="style6"></span>
			</p>
			<p>
				<br> <br> <input type=reset value=" Limpar "> <input type=submit
					value=" Entrar "> <span class="style6"></span>
			</p>
		</div>
	</form>
</body>
</html>