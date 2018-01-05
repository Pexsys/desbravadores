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
		<h3 class="page-header">Cadastro de Aprendizado e Afins</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
	<?php fDataFilters( 
		array( 
			"filterTo" => "#aprDatatable",
			"filters" => 
				array( 
					array( "id" => "Z", "ds" => "Tipo", "icon" => "fa fa-check" ),
					array( "id" => "X", "ds" => "Sexo", "icon" => "fa fa-venus-mars" ),
					array( "id" => "C", "ds" => "Classe", "icon" => "fa fa-graduation-cap" ),
					array( "id" => "G", "ds" => "Grupo", "icon" => "fa fa-group" ),
					array( "id" => "HA", "ds" => "Avaliações", "icon" => "fa fa-thermometer-half" ),
					array( "id" => "IC", "ds" => "Compras", "icon" => "fa fa-shopping-cart" ),
					array( "id" => "U", "ds" => "Unidade", "icon" => "fa fa-universal-access" ),
					array( "id" => "A", "ds" => "Áreas", "icon" => "fa fa-bookmark" ),
					array( "id" => "E", "ds" => "Especialidade", "icon" => "fa fa-check-circle-o" ),
					array( "id" => "M", "ds" => "Mestrado", "icon" => "fa fa-check-circle" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="aprDatatable">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th>Nome Completo</th>
					<th>Tipo</th>
					<th>Item</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
	<div class="row">
		<button class="btn btn-info" id="btnManual"><i class="glyphicon glyphicon-pencil"></i>&nbsp;Manual</button>
		&nbsp;
		<button class="btn btn-success" id="btnDigital"><i class="fa fa-barcode"></i>&nbsp;Digital</button>
		&nbsp;
		<button class="btn btn-danger" id="btnDelAprend"><i class="fa fa-trash-o"></i>&nbsp;Excluir</button>
	</div>
</div>
<div class="modal fade" id="aprendModal" role="dialog" data-backdrop="static"> <!---->
	<form method="post" id="cadAprendForm">
		<div class="modal-dialog"> <!---->
			<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
				</div>			
				<div class="modal-body" style="height:400px;overflow-y: auto;">
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>O qu&ecirc;?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-sm" name="oque" tab-function="checkbox" for="Attri">
									<input type="checkbox" autocomplete="off">Datas
								</label>
								<label class="btn btn-default btn-sm" name="oque" tab-function="checkbox" for="Class">
									<input type="checkbox" autocomplete="off">Classe
								</label>
								<label class="btn btn-default btn-sm" name="oque" tab-function="checkbox" for="Espec">
									<input type="checkbox" autocomplete="off">Especialidade
								</label>
								<label class="btn btn-default btn-sm" name="oque" tab-function="checkbox" for="Mestr">
									<input type="checkbox" autocomplete="off">Mestrado
								</label>
								<label class="btn btn-default btn-sm" name="oque" tab-function="checkbox" for="Merit">
									<input type="checkbox" autocomplete="off">M&eacute;ritos
								</label>
							</div>
							<br/>
							<div id="Attri" class="col-lg-12" style="display:none">
								<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Datas</b></div>
								<div class="row">
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtInicio">In&iacute;cio</label>
											<input type="checkbox" name="toggle-dates" for="dtInicio" field="fg_inicio_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtInicio" id="dtInicio" field="dt_inicio" class="form-control input-sm date" placeholder="In&iacute;cio"/>
										</div>
									</div>
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtConclusao">Conclus&atilde;o</label>
											<input type="checkbox" name="toggle-dates" for="dtConclusao" field="fg_conclusao_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtConclusao" id="dtConclusao" field="dt_conclusao" class="form-control input-sm date" placeholder="Conclus&atilde;o" style="display:none;"/>
										</div>
									</div>
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtAvaliacao">Avalia&ccedil;&atilde;o</label>
											<input type="checkbox" name="toggle-dates" for="dtAvaliacao" field="fg_avaliacao_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtAvaliacao" id="dtAvaliacao" field="dt_avaliacao" class="form-control input-sm date" placeholder="Avalia&ccedil;&atilde;o" style="display:none;"/>
										</div>
									</div>
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtInvestidura">Investidura</label>
											<input type="checkbox" name="toggle-dates" for="dtInvestidura" field="fg_investidura_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtInvestidura" id="dtInvestidura" field="dt_investidura" class="form-control input-sm date" placeholder="Investidura" style="display:none;"/>
										</div>
									</div>
								</div>
							</div>
							<div>
								<div id="Class" class="col-lg-12" style="display:none">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Classe</b></div>
									<div class="row">
										<div class="form-group col-xs-12">
											<select field="cd_classe" name="cmClasse" id="cmClasse" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm" multiple data-live-search="true" data-selected-text-format="count > 3" data-container="body" data-width="100%"></select>
										</div>
									</div>
									<div class="row" style="display:none" id="divIdent">
										<div class="form-group col-xs-12">
											<select field="tp_tag" name="cmIdent" id="cmIdent" opt-value="id" opt-label="ds" title="Itens a incluir na fila de impressão de identifica&ccedil;&atilde;o" class="selectpicker form-control input-xs" multiple data-selected-text-format="count > 3" data-container="body" data-width="100%"></select>
										</div>
									</div>
								</div>
								<div id="Espec" class="col-lg-12" style="display:none">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Especialidade</b></div>
									<div class="row">
										<div class="form-group col-xs-12">
											<select field="cd_espec" name="cmEspec" id="cmEspec" opt-value="id" opt-label="ds" opt-subtext="sb" class="selectpicker form-control input-sm" multiple data-live-search="true" data-selected-text-format="count > 2" data-container="body" data-width="100%"></select>
										</div>
									</div>
								</div>
								<div id="Mestr" class="col-lg-12" style="display:none">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Mestrado</b></div>
									<div class="row">
										<div class="form-group col-xs-12">
											<select field="cd_mest" name="cmMest" id="cmMest" opt-value="id" opt-label="ds" opt-subtext="sb" class="selectpicker form-control input-sm" multiple data-live-search="true" data-selected-text-format="count > 2" data-container="body" data-width="100%"></select>
										</div>
									</div>
								</div>
								<div id="Merit" class="col-lg-12" style="display:none">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Merito</b></div>
									<div class="row">
										<div class="form-group col-xs-12">
											<select field="cd_merito" name="cmMeri" id="cmMeri" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm" multiple data-container="body" data-width="100%" data-selected-text-format="count"></select>
										</div>
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
								<input type="radio" name="options" id="lista" autocomplete="off" checked>Filtro/Pesquisa
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
										<select field="id_pessoa" name="cmNome" id="cmNome" opt-value="id" opt-search="id" opt-label="ds" opt-subtext="sb" class="selectpicker form-control input-sm" multiple data-live-search="true" data-selected-text-format="count" data-container="body" data-width="100%"></select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-lg-12">
							<button type="submit" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</button>
							<?php /*
							<a role="button" class="btn btn-default pull-right" id="btnAprendizado"><i class="fa fa-graduation-cap"></i></a>
							<a role="button" class="btn btn-default pull-right" id="btnHistorico"><i class="fa fa-university"></i></a>
							<a role="button" class="btn btn-default pull-right" id="btnFinanceiro"><i class="fa fa-money"></i></a>
							*/?>
						</div>	
					</div>	
				</div>
			</div>
		</div>
	</form>
</div>	
<div class="modal fade" id="aprendBarModal" role="dialog" data-backdrop="static"> <!---->
	<div class="modal-dialog"> <!---->
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
			</div>			
			<div class="modal-body">
				<form method="post" id="cadAprendBarForm">
					<input type="hidden" id="barOp" field="op"/>
					<div class="panel panel-warning">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>O qu&ecirc;?</label>
						</div>
						<div class="panel-body">
							<div class="col-lg-12">
								<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Datas</b></div>
								<div class="row">
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtBarInicio">In&iacute;cio</label>
											<input type="checkbox" name="toggle-dates" for="dtBarInicio" field="fg_inicio_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtBarInicio" id="dtBarInicio" field="dt_inicio" class="form-control input-sm date" placeholder="In&iacute;cio"/>
										</div>
									</div>
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtBarConclusao">Conclus&atilde;o</label>
											<input type="checkbox" name="toggle-dates" for="dtBarConclusao" field="fg_conclusao_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtBarConclusao" id="dtBarConclusao" field="dt_conclusao" class="form-control input-sm date" placeholder="Conclus&atilde;o" style="display:none;"/>
										</div>
									</div>
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtBarAvaliacao">Avalia&ccedil;&atilde;o</label>
											<input type="checkbox" name="toggle-dates" for="dtBarAvaliacao" field="fg_avaliacao_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtBarAvaliacao" id="dtBarAvaliacao" field="dt_avaliacao" class="form-control input-sm date" placeholder="Avalia&ccedil;&atilde;o" style="display:none;"/>
										</div>
									</div>
									<div class="form-group col-xs-3">
										<div class="input-group center-block">
											<label for="dtBarInvestidura">Investidura</label>
											<input type="checkbox" name="toggle-dates" for="dtBarInvestidura" field="fg_investidura_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
											<input type="text" name="dtBarInvestidura" id="dtBarInvestidura" field="dt_investidura" class="form-control input-sm date" placeholder="Investidura" style="display:none;"/>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group col-lg-5">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-barcode fa-2x"></i></div>
									<input type="text" id="cdBar" name="cdBar" field="cd_bar" class="form-control input-lg" placeholder="C&oacute;digo" style="text-transform: uppercase"
										maxlength="<?php echo $GLOBALS['pattern']->getBars()->getLength();?>" 
										pattern="<?php echo $GLOBALS['pattern']->getBars()->getPattern("AE");?>"
										data-fv-regexp-message="C&oacute;digo inv&aacute;lido"
									/>
								</div>
							</div>
							<div class="col-lg-12" id="divResultado" style="display:none">
								<div class="panel panel-primary">
									<div class="panel-heading"><b>Resultado</b></div>
									<div class="panel-body" id="strResultado"></div>
								</div>
							</div>								
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>	
<script src="<?php echo $GLOBALS['pattern']->getVD();?>js/readdata.lib.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/cadastroAprendizado.js<?php echo "?".microtime();?>"></script>