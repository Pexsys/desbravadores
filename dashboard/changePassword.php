<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Alterar minha senha</h3>
	</div>
</div>
<div class="col-lg-3">
	<div class="row">
        <form class="form-horizontal" method="post" id="change-password">
            <div class="modal-body">
                <div class="form-group">
                  <label for="psw"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Nova Senha</label>
                  <input type="password" class="form-control" id="psw" name="psw" placeholder="Digite aqui sua senha">
                </div>
                <div class="form-group">
                  <label for="repeat"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;Repita a Nova Senha</label>
                  <input type="password" class="form-control" id="repeat" name="repeat" placeholder="Repita aqui sua nova senha">
                </div>
                <button type="submit" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/jquery.sha1.js"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/changePassword.js<?php echo "?".microtime();?>"></script>