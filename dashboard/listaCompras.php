<?php
@require_once("../include/filters.php");

function filtroDatasAvaliacao(){
	$result = CONN::get()->execute("
		SELECT DISTINCT ah.DT_AVALIACAO
		FROM APR_HISTORICO ah
		INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
		WHERE ah.DT_AVALIACAO IS NOT NULL
		AND (ah.DT_INVESTIDURA IS NULL OR YEAR(ah.DT_AVALIACAO) = YEAR(NOW()))
		ORDER BY 1 DESC
	");
	foreach($result as $k => $f):
		echo("<option value=\"".$f["DT_AVALIACAO"]."\"". ($f["DT_AVALIACAO"] <= date('Y-m-d') ? " selected" : "") .">". strftime("%d/%m/%Y",strtotime($f['DT_AVALIACAO'])) ."</option>");
	endforeach;
}
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
		<h3 class="page-header">Lista de Compras</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
	<?php fDataFilters( 
		array( 
			"filterTo" => "#comprasDatatable",
			"filters" => 
				array( 
					array( "id" => "Z", "ds" => "Tipo", "icon" => "fas fa-check" ),
					array( "id" => "X", "ds" => "Sexo", "icon" => "fas fa-venus-mars" ),
					array( "id" => "C", "ds" => "Classe", "icon" => "fas fa-graduation-cap" ),
					array( "id" => "G", "ds" => "Grupo", "icon" => "fas fa-object-group" ),
					array( "id" => "U", "ds" => "Unidade", "icon" => "fas fa-universal-access" ),
					array( "id" => "A", "ds" => "&Aacute;reas", "icon" => "fas fa-bookmark" ),
					array( "id" => "IC", "ds" => "Compras", "icon" => "fas fa-shopping-cart" ),
					array( "id" => "T", "ds" => "Membros Ativos", "icon" => "fas fa-toggle-on" ),
					array( "id" => "E", "ds" => "Especialidade", "icon" => "fas fa-check-circle" ),
					array( "id" => "M", "ds" => "Mestrado", "icon" => "fas fa-check-circle" ),
					array( "id" => "HT", "ds" => "Tipo de Material", "icon" => "fas fa-shopping-basket" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="comprasDatatable">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th>Nome Completo</th>
					<th>Item</th>
					<th>Comprado</th>
					<th>Entregue</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
	<div class="row">
		<button class="btn btn-default" id="btnProcess"><i class="fas fa-cog"></i>&nbsp;Processar Lista</button>
		&nbsp;
		<button class="btn btn-default" id="btnAdd"><i class="fas fa-plus"></i>&nbsp;Adicionar Item</button>
		&nbsp;
		<button class="btn btn-default" id="btnRedist"><i class="fas fa-cubes"></i>&nbsp;Distribuir Estoque</button>
		&nbsp;
		<button class="btn btn-warning" id="btnEdit"><i class="fas fa-pencil-alt"></i>&nbsp;Edi&ccedil;&atilde;o Manual</button>
		&nbsp;
		<button class="btn btn-success" id="btnEntrega"><i class="fas fa-pencil-alt"></i>&nbsp;Atualizar Entrega</button>
		&nbsp;
		<button class="btn btn-danger" id="btnDel"><i class="fas fa-trash-o"></i>&nbsp;Excluir</button>
		&nbsp;
		<button class="btn btn-default pull-right" id="btnListagens"><i class="fas fa-print"></i>&nbsp;Listagens</button>
	</div>
</div>

<div class="modal fade" id="printModal" role="dialog" data-backdrop="static"> <!---->
	<form method="post" id="printForm">
		<div class="modal-dialog">
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
							<div>
								<div class="row">
									<div class="form-group col-xs-12">
										<select name="cmLista" id="cmLista" opt-value="cd" opt-label="ds" class="selectpicker form-control input-sm" data-container="body" data-width="100%">
										  <option value=""></option>
											<option value="geraListaTirasNome.php?">LISTA DE COMPRAS - TIRAS DE NOME</option>
											<option value="geraListaComprasMaterial.php?" show="divMaterial">LISTA DE COMPRAS - POR TIPO DE MATERIAL</option>
											<option value="geraListaComprasAlmArea.php?fc=N" show="divLista">LISTA DE COMPRAS - ALMOXARIFADO DA ASSOCIA&Ccedil;&Atilde;O - POR &Aacute;REA</option>
											<option value="geraListaComprasAlmGaveta.php" show="divLista">LISTA DE COMPRAS - ALMOXARIFADO DA ASSOCIA&Ccedil;&Atilde;O - POR GAVETA</option>
											<option value="geraListaComprasAlmArea.php?fc=S">LISTA DE COMPRAS - ITENS COMPRADOS - POR &Aacute;REA</option>
											<option value="geraListaComprasMDA.php" show="divMDA">LISTA DE COMPRAS - SECRETARIA MDA ASSOCIA&Ccedil;&Atilde;O / EQUIPE REGIONAL</option>
											<option value="geraListaInvestDSA.php" show="divFilter">LISTA DE INVESTIDURAS - CADASTRO DSA</option>
											<option value="geraListaInvestSec.php" show="divFilter">LISTA DE INVESTIDURAS - SECRETARIA DO CLUBE</option>
										</select>
									</div>
								</div>
							</div>
						</div>	
					</div>
					<div class="panel panel-warning" name="rowFilter" id="divFilter" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Filtros</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div>
								<div class="row">
									<div class="form-group col-xs-12">
										<label class="control-label">Datas de Avalia&ccedil;&atilde;o</label>
										<select field="cmFiltro" class="selectpicker form-control input-sm" multiple data-actions-box="true" data-selected-text-format="count > 3" data-container="body" data-width="100%">
											<?php filtroDatasAvaliacao();?>
										</select>
									</div>
								</div>
							</div>
						</div>	
					</div>
					<div class="panel panel-warning" name="rowFilter" id="divMDA" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Filtros</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div>
								<div class="row">
									<div class="form-group col-xs-12">
										<label class="control-label">Datas de Avalia&ccedil;&atilde;o</label>
										<select field="cmFiltroMDA" class="selectpicker form-control input-sm" multiple data-actions-box="true" data-selected-text-format="count > 3" data-container="body" data-width="100%">
											<?php filtroDatasAvaliacao();?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-12">
										<label class="control-label">Status de Investidura</label>
										<select field="cmINV" class="selectpicker form-control input-sm" data-container="body" data-width="100%">
											<option value="">AMBOS</option>
											<option value="S">J&Aacute; INVESTIDOS</option>
											<option value="N" selected="selected">N&Atilde;O INVESTIDOS</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-warning" name="rowFilter" id="divLista" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Filtros</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div>
								<div class="row">
									<div class="form-group col-xs-12">
										<label class="control-label">Itens</label>
										<select field="cmPREV" class="selectpicker form-control input-sm" data-container="body" data-width="100%">
											<option value="T">TODOS OS ITENS</option>
											<option value="P">SOMENTE OS PREVISTOS</option>
											<option value="A">SOMENTE OS AVALIADOS</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-warning" name="rowFilter" id="divMaterial" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Filtros</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div>
								<div class="row">
									<div class="form-group col-xs-12">
										<label class="control-label">Tipos</label>
										<select field="cmTIPOS" id="cmTIPOS" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm" data-container="body" data-width="100%" title="(NENHUM)" multiple data-actions-box="true" data-selected-text-format="count > 3"></select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-lg-12">
							<button id="btnGerar" class="btn btn-success pull-right"><i class="fas fa-file-pdf-o"></i>&nbsp;Gerar PDF</button>
						</div>	
					</div>	
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="comprasModal" role="dialog" tabindex="-1">
	<form method="post" id="controleForm">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body">
					<div class="panel panel-warning">
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="row">
								<div class="form-group col-xs-6">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:12px"><b>Comprado</b></div>
									<input type="checkbox" id="fgCompra" field="fg_compra" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-width="105" data-size="small" data-on="SIM" data-off="N&Atilde;O"/>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-xs-6">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:12px"><b>Entregue</b></div>
									<input type="checkbox" id="fgEntregue" field="fg_entregue" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-width="105" data-size="small" data-on="SIM" data-off="N&Atilde;O"/>
								</div>
							</div>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="listaModal" role="dialog" data-backdrop="static">
	<form method="post" id="cadListaForm">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
				</div>
				<div class="modal-body">
					<div class="panel panel-warning" id="divOQue" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>O qu&ecirc;?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="row">
								<div class="form-group col-xs-12">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Tipo</b></div>
									<select name="cmTipo" id="cmTipo" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm" data-container="body" data-width="100%" title="(NENHUM)"></select>
								</div>
							</div>
							<div class="row" id="divItem" style="display:none">
								<div class="form-group col-xs-4">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Quantidade</b></div>
									<input type="text" name="qtItens" id="qtItens" field="qt_itens" class="form-control" placeholder="Uniformes" data-min="1"/>
								</div>
								<div class="form-group col-xs-8">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Item</b></div>
									<select field="id" name="cmItem" id="cmItem" opt-value="id" opt-links="cm" opt-label="ds" opt-search="sb" opt-subtext="sb" class="selectpicker form-control input-sm" data-live-search="true" title="(NENHUM)" data-container="body" data-width="100%"></select>
								</div>
							</div>
							<div class="row" id="divCmpl" style="display:none">
								<div class="form-group col-xs-12">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Conte&uacute;do do Item</b></div>
									<input type="text" name="cmpl" id="cmpl" field="cmpl" class="form-control input-sm" placeholder="Digite aqui.."/>
								</div>
							</div>
						</div>
					</div>
          <div class="panel panel-warning" id="divTipoMaterial" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Material</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
              <div class="row">
								<div class="form-group col-xs-12">
									<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Tipo</b></div>
									<select field="tp" name="cmTipoMaterial" id="cmTipoMaterial" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm"  multiple data-actions-box="true" data-live-search="true" data-selected-text-format="count" data-container="body" data-width="100%" title="(NENHUM)"></select>
								</div>
							</div>
            </div>
					</div>
					<div class="panel panel-warning" id="divParaQuem" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Para quem?</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="row">
								<div class="form-group col-xs-12">
									<select field="id_cad_membro" name="cmNome" id="cmNome" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm" multiple data-actions-box="true" opt-subtext="sb" data-live-search="true" data-selected-text-format="count" data-container="body" data-width="100%"></select>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-warning"  id="divDataQuando" style="display:none">
						<div class="panel-heading" style="padding:4px 10px 0px">
							<label>Data da Entrega</label>
						</div>
						<div class="panel-body" style="padding:4px 10px 0px">
							<div class="row">
								<div class="form-group col-xs-3">
									<input type="text" name="dtQuando" id="dtQuando" field="dt_quando" class="form-control input-sm date" placeholder="Quando?"/>
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
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/listaCompras.js<?php echo "?".microtime();?>"></script>
