<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Manuten&ccedil;&atilde;o de Usu&aacute;rios Ativos</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="usrTable">
			<thead>
				<tr>
					<th></th>
					<th>Usu&aacute;rio</th>
					<th>&Uacute;ltimo Acesso</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
	<div class="row">
		<button class="btn btn-info" id="btnChangePass"><i class="fas fa-key"></i>&nbsp;Trocar Senha</button>
		<button class="btn btn-danger pull-right" id="btnDelUsers"><i class="fas fa-trash-o"></i>&nbsp;Excluir</button>
	</div>
</div>

<div class="modal fade" id="comModal" role="dialog" data-backdrop="static">
	<form method="post" id="change-password">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
				</div>
				<div class="modal-body">
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Trocar senha</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="col-lg-12">
								<div class="form-group">
								  <input type="hidden" id="cd" name="cd"/>
                                  <label for="psw"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Nova Senha</label>
                                  <input type="password" class="form-control" id="psw" name="psw" placeholder="Digite aqui sua senha">
                                </div>
                                <div class="form-group">
                                  <label for="repeat"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Repita a Nova Senha</label>
                                  <input type="password" class="form-control" id="repeat" name="repeat" placeholder="Repita aqui sua nova senha">
                                </div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-lg-12">
							<button type="submit" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</button>
						</div>	
					</div>	
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/jquery.sha1.js"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/changePassword.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/users.js<?php echo "?".microtime();?>"></script>