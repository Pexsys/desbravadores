<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro do Estoque de Materiais</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
		<?php FILTERS::Data(
			array(
				"filterTo" => "#matEstTable",
				"filters" =>
					array(
						array( "id" => "HT", "ds" => "Tipo de Material", "icon" => "fa fa-shopping-basket" )
					)
			)
		);?>
		</div>
		<div class="row">
			<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="matEstTable">
				<thead>
					<tr>
						<th></th>
						<th>Tipo</th>
						<th>Item</th>
						<th>Estoque</th>
					</tr>
				</thead>
			</table>
			<br/>
		</div>
		<div class="row">
			<button class="btn btn-warning" id="btnAdd"><i class="fa fa-plus"></i>&nbsp;Adicionar Item</button>
		</div>
	</div>

	<div class="modal fade" id="listaModal" role="dialog" data-backdrop="static">
		<form method="post" id="cadListaForm">
			<div class="modal-dialog"> <!---->
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
								<div class="row">
									<div class="form-group col-xs-12">
										<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Tipo</b></div>
										<select name="cmTipo" id="cmTipo" opt-value="id" opt-label="ds" class="selectpicker form-control input-sm" data-container="body" data-width="100%" title="(NENHUM)"></select>
									</div>
								</div>
								<div class="row" id="divItem" style="display:none">
									<div class="form-group col-xs-4">
										<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Quantidade</b></div>
										<input type="text" name="qtItens" id="qtItens" field="qt_itens" class="form-control" placeholder="Uniformes" data-min="0"/>
									</div>
									<div class="form-group col-xs-8">
										<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Item</b></div>
										<select field="id" name="cmItem" id="cmItem" opt-value="id" opt-links="cm" opt-label="ds" opt-search="sb" opt-subtext="sb" class="selectpicker form-control input-sm" data-live-search="true" title="(NENHUM)" data-container="body" data-width="100%"></select>
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
</div>
<script src="<?php echo PATTERNS::getVD();?>admin/view/screens/estoque/index.js<?php echo "?".time();?>"></script>
