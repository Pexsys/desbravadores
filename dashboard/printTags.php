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
		<h3 class="page-header">Imprimir Identifica&ccedil;&atilde;o</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="tagDatatable">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th>Nome Completo</th>
					<th>Identifica&ccedil;&atilde;o</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
	<div class="row">
		<div class="col-lg-4">
			<button class="btn btn-primary" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Adicionar Identifica&ccedil;&atilde;o</button>
		</div>
		<div class="col-lg-4">
			<button class="btn btn-danger" id="btnClear" style="display:none"><i class="fa fa-trash-o"></i>&nbsp;Limpar Tudo</button>
			&nbsp;
			<button class="btn btn-warning" id="btnDel" style="display:none"><i class="glyphicon glyphicon-remove"></i>&nbsp;Apagar Linha(s)</button>
		</div>
		<div class="col-lg-4">
			<button class="btn btn-success pull-right" id="btnPrepare" style="display:none"><i class="fa fa-list-alt"></i>&nbsp;Preparar</button>
		</div>
	</div>
</div>

<div class="modal fade" id="tagsModal" role="dialog" data-backdrop="static"><!---->
	<form method="post" id="addTagsForm">
		<div class="modal-dialog"><!---->
			<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
				</div>
				<div class="modal-body">
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>O qu&ecirc;?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="col-lg-12">
								<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Tipo de Identifica&ccedil;&atilde;o</b></div>
								<div class="row">
									<div class="form-group col-xs-12">
										<select name="cbTags" id="cbTags" class="selectpicker form-control input-sm" opt-value="id" opt-label="ds" opt-links="cl;md" multiple field="tp" title="Escolha uma ou mais etiquetas" data-selected-text-format="count > 2" data-width="100%" data-container="body"></select>
									</div>
								</div>
							</div>
							<div class="col-lg-12" style="display:none" id="divAprend">
								<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Aprendizado</b></div>
								<div class="row">
									<div class="form-group col-xs-12">
										<select name="cbAprend" id="cbAprend" class="selectpicker form-control input-sm" opt-value="id" opt-label="ds" multiple data-live-search="true" field="ia" title="Escolha um ou mais aprendizados" data-selected-text-format="count > 2" data-width="100%" data-container="body"></select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Para quem?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<?php fDataFilters( 
								array( 
									"filterTo" => "#cbNomes",
									"filters" => 
										array( 
											array( "value" => "G", "label" => "Grupo" ),
											array( "value" => "X", "label" => "Sexo" ),
											array( "value" => "U", "label" => "Unidade" ),
											array( "value" => "IC", "label" => "Compras" ),
											array( "value" => "C", "label" => "Classe" )
										)
								) 
							);
							?>
							<div class="form-group col-xs-12">
								<select name="cbNomes" id="cbNomes" class="selectpicker form-control input-sm" opt-value="id" opt-search="id" opt-label="nm" opt-selected="fg" multiple data-live-search="true" field="ip" title="Escolha um ou mais nomes" data-selected-text-format="count > 2" data-width="100%" data-container="body" data-actions-box="true"></select>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-lg-12">
							<button type="addList" class="btn btn-success pull-right"><i class="fa fa-cart-arrow-down"></i>&nbsp;Colocar na lista</button>
						</div>	
					</div>	
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="prepareModal" role="dialog" data-backdrop="static"> <!---->
	<form method="post" id="cadPrepareForm">
		<div class="modal-dialog"> <!---->
			<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
				</div>			
				<div class="modal-body" style="height:400px;overflow-y:auto;"> <!-- -->
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>O qu&ecirc;?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 10px">
							<div class="col-lg-12">
								<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Tipo de Formul&aacute;rio</b></div>
								<div class="row">
									<div class="form-group col-xs-12">
										<select field="tp_form" name="cmForm" id="cmForm" opt-value="id" opt-label="ds" opt-links="fi;qt" title="Selecione o tipo de formul&aacute;rio..." class="selectpicker form-control input-sm" data-container="body" data-width="100%"></select>
									</div>
								</div>
							</div>
							<div style="display:none" id="divPageControl">
								<div class="col-lg-12">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><button class="btn btn-default btn-xs" id="btnAddFI"><i class="fa fa-plus"></i></button>&nbsp;<b>P&aacute;gina Incompleta</b></div>
									<div class="row" id="divIncomplete" qt-sq="0">
									</div>
								</div>
							</div>
						</div>	
					</div>
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Para quem?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
							  <label class="btn btn-default btn-sm" name="quem" tab-function="radio" for="Lista">
								<input type="radio" name="options" id="lista" autocomplete="off" checked>Lista Atual
							  </label>
							  <label class="btn btn-default btn-sm" name="quem" tab-function="radio" for="Selec">
								<input type="radio" name="options" id="selec" autocomplete="off">Linhas Marcadas
							  </label>
							  <label class="btn btn-default btn-sm" name="quem" tab-function="radio" for="People">
								<input type="radio" name="options" id="peopl" autocomplete="off">Membros Espec&iacute;ficos
							  </label>
							</div>
							<br/>
							<div id="People" class="col-lg-12" style="display:none">
								<div class="row">
									<div class="form-group col-xs-12">
										<label for="cmNome" class="control-label">Membros</label>
										<select name="cmNome" id="cmNome" opt-value="id" opt-search="id" opt-label="nm" class="selectpicker form-control input-sm" multiple data-live-search="true" field="ip" title="Escolha um ou mais nomes" data-selected-text-format="count > 2" data-width="100%" data-container="body" data-actions-box="true"></select>
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
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/printTags.js<?php echo "?".microtime();?>"></script>