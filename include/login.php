<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/jquery.sha1.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>js/login.js?<?php echo microtime();?>"></script>
<!-- Modal -->
<div class="modal fade" id="myLoginModal">
	<div class="modal-dialog modal-sm">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header" style="padding:35px 50px;">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4><span class="glyphicon glyphicon-lock"></span> Login</h4>
			</div>
			<form class="form-signin" method="post" id="login-form">
				<input type="hidden" id="page" value=""/>
				<div class="modal-body" style="padding:40px 50px;">
					<div class="form-group">
					  <label for="usr"><span class="glyphicon glyphicon-user"></span>&nbsp;Usu&aacute;rio</label>
					  <input type="text" class="form-control" id="usr" name="usr" placeholder="Digite aqui seu c&oacute;digo">
					</div>
					<div class="form-group">
					  <label for="psw"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Senha</label>
					  <input type="password" class="form-control" id="psw" name="psw" placeholder="Digite aqui sua senha">
					</div>
					<?php //echo "<div class=\"checkbox\"><label><input type=\"checkbox\" value=\"\" checked>Lembrar</label></div>";?>
					<button type="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span>&nbsp;Entrar</button>
				</div>
				<?php
				//<div class="modal-footer">
					//echo "<p>Ainda n&atilde;o registrado? <a href=\"#\">Registre-se</a></p>";
					//echo "<p>Esqueceu a <a href=\"#\">Senha?</a></p>";
				//</div>
				?>
			</form>
		</div>
	</div>
</div>