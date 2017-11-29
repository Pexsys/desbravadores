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
		<h3 class="page-header">Cadastro de Ocorr&ecirc;ncias</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
	<?php
	fDataFilters( 
		array( 
			"filterTo" => "#ocoDataTable",
			"filters" => 
				array( 
					array( "value" => "X", "label" => "Sexo" ),
					array( "value" => "U", "label" => "Unidade" ),
					array( "value" => "TO", "label" => "Tipo" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="ocoDataTable">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th>C&oacute;digo</th>
					<th>Nome</th>
					<th>Tipo</th>
					<th>Registro</th>
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
<div class="modal fade" id="ocoModal" role="dialog" data-backdrop="static"><!---->
	<div class="modal-dialog modal-lg"><!---->
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" id="cadOcoForm">
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-warning" aria-expanded="false">
								<div class="panel-heading">
									<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
									<h3 class="panel-title"><b>Ocorr&ecirc;ncia</b></h3>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="form-group col-xs-2">
											<label for="ocoID" class="control-label">Sequ&ecirc;ncia</label>
											<input type="text" name="ocoID" id="ocoID" field="id" class="form-control input-sm" placeholder="ID" disabled="disabled" style="text-align:center"/>
										</div>
										<div class="form-group col-xs-2">
											<label for="cd" class="control-label">C&oacute;digo</label>
											<input type="text" name="ocoCD" id="ocoCD" field="cd" class="form-control input-sm" placeholder="C&oacute;digo" disabled="disabled" style="text-align:center"/>
										</div>
										<div class="form-group col-xs-3">
											<label for="ocoDH" class="control-label">Data</label>
											<div class="input-group date" id="datetimepicker" datatype="datetimepicker">
												<input type="text" name="ocoDH" id="ocoDH" field="dh" class="form-control input-sm" placeholder="Data"/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
										<div class="form-group col-xs-2">
											<label for="tpOcor" class="control-label">Tipo</label>
											<input type="checkbox" name="tpOcor" id="tpOcor" field="tp" value-on="P" value-off="N" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="<b>POSITIVA</b>" data-off="NEGATIVA" data-size="small" data-width="120"/>
										</div>
										<div class="form-group col-xs-2">
											<label for="fgPend" class="control-label">Status</label>
											<input type="checkbox" name="fgPend" id="fgPend" field="fg_pend" value-on="N" value-off="S" data-toggle="toggle" data-onstyle="success" data-offstyle="warning" data-on="<b>EFETIVADO</b>" data-off="RASCUNHO" data-size="small" data-width="120"/>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-12">
											<label for="cmNome" class="control-label">Para</label>
											<select field="id_pessoa" name="cmNome" id="cmNome" opt-value="id_pessoa" opt-search="nm" opt-label="nm" class="selectpicker form-control input-sm" data-live-search="true" data-container="body" data-width="100%"></select>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-12"><textarea field="txt" id="txt" type="wysiwyg"></textarea></div>
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

<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/tinymce/tinymce.min.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/cadastroOcorrencias.js<?php echo "?".microtime();?>"></script>