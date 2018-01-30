<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro de Comunicados</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
			<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="comDataTable">
				<thead>
					<tr>
						<th></th>
						<th></th>
						<th>C&oacute;digo</th>
						<th>Data de Emiss&atilde;o</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody/>
			</table>
			<br/>
		</div>
		<div class="row">
			<a role="button" class="btn btn-warning pull-left" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Novo</a>
		</div>
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
										<h3 class="panel-title"><b>Comunicado</b></h3>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="form-group col-xs-2">
												<label for="comID" class="control-label">Sequ&ecirc;ncia</label>
												<input type="text" name="comID" id="comID" field="id" class="form-control input-sm" placeholder="ID" disabled="disabled" style="text-align:center"/>
											</div>
											<div class="form-group col-xs-2">
												<label for="cd" class="control-label">C&oacute;digo</label>
												<input type="text" name="comCD" id="comCD" field="cd" class="form-control input-sm" placeholder="C&oacute;digo" disabled="disabled" style="text-align:center"/>
											</div>
											<div class="form-group col-xs-3">
												<label for="comDH" class="control-label">Data</label>
												<div class="input-group date" id="datetimepicker" datatype="datetimepicker">
													<input type="text" name="comDH" id="comDH" field="dh" class="form-control input-sm" placeholder="Data"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												</div>
											</div>
											<div class="form-group col-xs-2">
												<label for="fgPend" class="control-label">Status</label>
												<input type="checkbox" name="fgPend" id="fgPend" field="fg_pend" value-on="N" value-off="S" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="<b>EFETIVADO</b>" data-off="RASCUNHO" data-size="small" data-width="130"/>
											</div>
											<div class="col-xs-3"></div>
										</div>
										<div class="row">
											<div class="form-group col-xs-12"><textarea field="txt" id="txt" type="wysiwyg"></textarea></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-3">
								<?php if (true): //PODE EXCLUIR?>
								<a role="button" class="btn btn-danger pull-left" data-toggle="modal" id="btnDel"><i class="glyphicon glyphicon-trash"></i>&nbsp;Excluir</a>
								<?php endif;?>
							</div>
							<?php if (true): //PODE GERAR?>
							<div class="col-xs-6">
								<div id="divPrint">
									<div  class="form-group col-xs-4">
										<input type="text" name="nrCopias" id="nrCopias" class="form-control" placeholder="Cópias" value="1" data-min="1" data-max="200"/>
									</div>
									<a role="button" class="btn btn-info" data-toggle="modal" id="btnPrint"><i class="glyphicon glyphicon-print"></i>&nbsp;Gerar Comunicados</a>
								</div>
							</div>
							<?php endif;?>
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
</div>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/tinymce/tinymce.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/cadastroComunicados.js<?php echo "?".microtime();?>"></script>
