<?php
@require_once("../include/filters.php");
?>
<style>
.btn-default.active { 
    background-color: #337ab7;
    border-color: #2e6da4;
    color: #fff;
}
</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro do Di&aacute;rio de Classe</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="diaDataTable">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th>Seq.</th>
					<th>Classe</th>
					<th>Requisito</th>
					<th>Data</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody/>
		</table>
		<br/>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<a role="button" class="btn btn-warning pull-left" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Nova</a>
		</div>
		<div class="col-xs-6">
			<a role="button" class="btn btn-info pull-right" data-toggle="modal" id="btnPrepare" style="display:none"><i class="glyphicon glyphicon-print"></i>&nbsp;Preparar</a>
		</div>
	</div>
</div>
<div class="modal fade" id="diaModal" role="dialog" data-backdrop="static"><!---->
	<div class="modal-dialog modal-lg"><!---->
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" id="cadRegForm">
				<input type="hidden" name="regID" id="regID" field="id"/>
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-warning" aria-expanded="false">
								<div class="panel-heading">
									<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
									<h3 class="panel-title"><b>Di&aacute;rio</b></h3>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="form-group col-xs-6">
											<label for="cmClasse" class="control-label">Classe</label>
											<select field="id_classe" name="cmClasse" id="cmClasse" class="form-control input-sm"></select>
										</div>
										<div class="form-group col-xs-1">
											<label for="seqID" class="control-label">Seq.</label>
											<input type="text" name="seqID" id="seqID" field="sq" class="form-control input-sm" placeholder="" disabled="disabled" style="text-align:center"/>
										</div>
										<div class="form-group col-xs-3">
											<label for="regDH" class="control-label">Data</label>
											<div class="input-group date" id="datetimepicker" datatype="datetimepicker">
												<input type="text" name="regDH" id="regDH" field="dh" class="form-control input-sm" placeholder="Data"/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
										<div class="form-group col-xs-2">
											<label for="fgPend" class="control-label">Status</label>
											<input type="checkbox" name="fgPend" id="fgPend" field="fg_pend" value-on="N" value-off="S" data-toggle="toggle" data-onstyle="success" data-offstyle="warning" data-on="<b>EXECUTADO</b>" data-off="PLANEJADO" data-size="small" data-width="120"/>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-12">
											<label for="cmReq" class="control-label">Requisito</label>
											<select field="id_req" name="cmReq" id="cmReq" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm" data-live-search="true" data-container="body" data-width="100%"></select>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-12"><textarea field="txt" id="txt" type="wysiwyg" placeholder="Descrição"></textarea></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<?php if (true): //PODE EXCLUIR?>
							<a role="button" class="btn btn-danger pull-left" data-toggle="modal" id="btnDel"><i class="glyphicon glyphicon-trash"></i>&nbsp;Excluir</a>
							<?php endif;?>
						</div>
						<div class="col-xs-6">
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

<div class="modal fade" id="prepareModal" role="dialog" data-backdrop="static"> <!---->
	<form method="post" id="cadPrepareForm">
		<div class="modal-dialog"> <!---->
			<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
				</div>			
				<div class="modal-body"> <!-- -->
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Para quem?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
							  <label class="btn btn-default btn-sm" name="quem" tab-function="radio" for="Lista">
								<input type="radio" name="options" id="lista" autocomplete="off" checked>Lista Atual
							  </label>
							  <label class="btn btn-default btn-sm" name="quem" tab-function="radio" for="People">
								<input type="radio" name="options" id="peopl" autocomplete="off">Membros Espec&iacute;ficos
							  </label>
							</div>
							<br/>
							<div id="People" class="col-lg-12" style="display:none">
								<div class="row">
									<div class="form-group col-xs-12">
										<label for="cmName" class="control-label">Membros</label>
										<select name="cmName" id="cmName" opt-value="value" opt-search="label" opt-label="label" class="selectpicker form-control input-sm" data-live-search="true" field="ip" title="Escolha um nome" data-width="100%" data-container="body" data-actions-box="true"></select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-lg-12">
							<button id="btnGerar" class="btn btn-success pull-right"><i class="fa fa-print"></i>&nbsp;Gerar</button>
						</div>	
					</div>	
				</div>
			</div>
		</div>
	</form>
</div>

<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/tinymce/tinymce.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/cadastroDiarioClasse.js<?php echo "?".microtime();?>"></script>