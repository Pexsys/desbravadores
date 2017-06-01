<?php
@require_once("../include/filters.php");
?>
<style>
.autoriz{
  background: #ffffaa !important;
}
</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro de Eventos/Sa&iacute;das</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="saidasDatatable">
			<thead>
				<tr>
					<th>C&oacute;d.</th>
					<th>Descri&ccedil;&atilde;o</th>
					<th>Sa&iacute;da</th>
					<th>Retorno</th>
				</tr>
			</thead>
			<tbody/>
		</table>
		<br/>
	</div>
	<div class="row">
		<a role="button" class="btn btn-warning pull-left" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Novo</a>
		<a role="button" class="btn btn-primary pull-right" name="filtro" id="btnAtivos" tp-filtro="Y"><i class="fa fa-filter"></i>&nbsp;<?php echo date("Y");?></a>
		<a role="button" class="btn btn-primary-outline pull-right" name="filtro" id="btnTodos" tp-filtro="T"><i class="fa fa-globe"></i>&nbsp;Todos</a>
	</div>
</div>
<div class="modal fade" id="saidasModal" role="dialog" data-backdrop="static"><!---->
	<div class="modal-dialog"><!---->
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" id="cadSaidasForm">
					<input type="hidden" field="id_cad_eventos"/>
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-red" aria-expanded="false">
								<div class="panel-heading">
									<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
									<h3 class="panel-title"><b>Evento/Sa&iacute;da</b></h3>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="form-group col-xs-2">
											<label for="saidaID" class="control-label">C&oacute;digo</label>
											<input type="text" name="saidaID" id="saidaID" field="id" class="form-control input-sm" placeholder="ID" disabled="disabled" style="text-align:center"/>
										</div>
										<div class="form-group col-xs-10">
											<label for="dsEvento" class="control-label">Descri&ccedil;&atilde;o</label>
											<input type="text" name="dsEvento" id="dsEvento" field="ds" class="form-control input-sm" placeholder="Descri&ccedil;&atilde;o"/>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-6">
											<label for="dhSaida" class="control-label">Data/Hora Sa&iacute;da</label>
											<div class="input-group date" id="datetimepickerini" datatype="datetimepicker">
												<input type="text" name="dhSaida" id="dhSaida" field="dh_s" class="form-control input-sm" placeholder="Data/Hora da Sa&iacute;da"/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
										<div class="form-group col-xs-6">
											<label for="dhRetorno" class="control-label">Data/Hora Retorno</label>
											<div class="input-group date" id="datetimepickerfim" datatype="datetimepicker">
												<input type="text" name="dhRetorno" id="dhRetorno" field="dh_r" class="form-control input-sm" placeholder="Data/Hora do Retorno"/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="panel panel-yellow" aria-expanded="false">
								<div class="panel-body" style="height:190px;overflow-y:scroll;">
									<div class="row">
										<div class="form-group col-xs-6">
											<label for="dsLocal" class="control-label">Local do evento</label>
											<input type="text" name="dsLocal" id="dsLocal" field="ds_dest" class="form-control input-sm" placeholder="Local do Evento"/>
										</div>
										<div class="form-group col-xs-6">
											<label for="dsEncontro" class="control-label">Local de Sa&iacute;da e Retorno</label>
											<input type="text" name="dsEncontro" id="dsEncontro" field="ds_orig" class="form-control input-sm" placeholder="Local de Sa&iacute;da e Retorno"/>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-6">
											<label for="dsTema" class="control-label">Tema</label>
											<input type="text" name="dsTema" id="dsTema" field="ds_tema" class="form-control input-sm" placeholder="Tema do Evento"/>
										</div>
										<div class="form-group col-xs-6">
											<label for="dsOrgan" class="control-label">Organiza&ccedil;&atilde;o</label>
											<input type="text" name="dsOrgan" id="dsOrgan" field="ds_org" class="form-control input-sm" placeholder="Organiza&ccedil;&atilde;o do Evento"/>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-6">
											<input type="checkbox" name="cbAtivo" id="cbAtivo" field="fg_campori" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="warning" data-offstyle="default" data-on="<b>AUTORIZAÇÃO CAMPORI</b>" data-off="AUTORIZAÇÃO COMUM" data-size="small" data-width="200"/>
										</div>
										<div class="form-group col-xs-6">
											<input type="checkbox" name="cbImprimir" id="cbImprimir" field="fg_imprimir" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="warning" data-offstyle="default" data-on="IMPRESSÃO EXTERNA" data-off="IMPRESSÃO INTERNA" data-size="small" data-width="200"/>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
										<?php fDataFilters( 
											array( 
												"filterTo" => "#cbParticip",
												"filters" => 
													array( 
														array( "value" => "G", "label" => "Grupo",		"icon" => "fa fa-group" ),
														array( "value" => "X", "label" => "Sexo",		"icon" => "fa fa-user" ),
														array( "value" => "B", "label" => "Batizado",	"icon" => "fa fa-smile-o" ),
														array( "value" => "U", "label" => "Unidade",	"icon" => "fa fa-universal-access" ),
														array( "value" => "C", "label" => "Classe",		"icon" => "fa fa-universal-access" )
													)
											) 
										);
										?>
										</div>
									</div>
									<div class="row">
										<div class="form-group col-xs-12">
											<label for="cbParticip" class="control-label"><font class="autoriz">&nbsp;Autoriza&ccedil;&atilde;o&nbsp;</font>&nbsp;&nbsp;|&nbsp;&nbsp;Participa&ccedil;&atilde;o<i class="fa fa-check" aria-hidden="true"></i></label>
											<select name="cbParticip" id="cbParticip" class="selectpicker form-control input-sm" opt-value="id" opt-label="nm" opt-selected="pt" opt-links="fg" opt-link-color="S=autoriz" multiple data-live-search="true" field="particip" title="Escolha um ou mais nomes" data-selected-text-format="count > 2" data-width="100%" data-container="body" data-actions-box="true"></select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="col-lg-12">
							<?php if (true): //PODE EXCLUIR?>
							<a role="button" class="btn btn-danger pull-left" data-toggle="modal" id="btnDel"><i class="glyphicon glyphicon-trash"></i>&nbsp;Excluir</a>
							<?php endif;?>
							<?php if (true): //PODE INSERIR/ALTERAR?>
							<button type="submit" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</button>
							<?php endif;?>
							<?php if (true): //PODE GERAR?>
							<a role="button" class="btn btn-info pull-right" data-toggle="modal" id="btnPrint"><i class="glyphicon glyphicon-print"></i>&nbsp;Gerar Autoriza&ccedil;&otilde;es</a>
							<?php endif;?>
						</div>	
					</div>	
				</form>
			</div>	
		</div>
	</div>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/cadastroSaidas.js<?php echo "?".microtime();?>"></script>