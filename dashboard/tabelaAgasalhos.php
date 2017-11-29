<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Tabela de Tamanhos/Modelos de Agasalhos</h3>
	</div>
</div>
<div class="col-lg-2">
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="comDataTable">
			<thead>
				<tr>
					<th>C&oacute;digo</th>
				</tr>
			</thead>
			<tbody/>
		</table>
		<br/>
	</div>
	<?php
	/*
	<div class="row">
		<a role="button" class="btn btn-warning pull-left" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Novo</a>
	</div>
	*/
	?>
</div>
<div class="modal fade" id="comModal" role="dialog" data-backdrop="static"><!---->
	<div class="modal-dialog modal-lg"><!---->
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" id="cadComForm">
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-warning" aria-expanded="false">
								<div class="panel-heading">
									<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
									<h3 class="panel-title"><b>Agasalhos</b></h3>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="form-group col-xs-2">
											<label for="cd" class="control-label">C&oacute;digo</label>
											<input type="text" name="comCD" id="comCD" field="cd" class="form-control input-sm" placeholder="C&oacute;digo" disabled="disabled" style="text-align:center"/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3">
							<?php if (true): //PODE INSERIR/ALTERAR?>
							<a role="button" type="submit" id="btnGravar" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</a>
							<?php endif;?>
						</div>	
					</div>	
				</form>
			</div>	
		</div>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/tabelaAgasalhos.js<?php echo "?".microtime();?>"></script>