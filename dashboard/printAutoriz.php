<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Imprimir Autoriza&ccedil;&atilde;o</h3>
	</div>
</div>
<form class="form-horizontal" method="post" id="capas-form">	
	<div class="col-xs-12 col-md-12">
		<div class="row form-group" style="margin-bottom:20px">
			<table class="compact row-border hover stripe" style="cursor:pointer" cellspacing="0" width="100%" id="simpledatatable">
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
		</div>
	</div>
	<div class="col-xs-12 col-md-12">
		<div class="row form-group">
			<label for="nmMembro" class="control-label">Para quem?</label>
			<select name="nmMembro" id="nmMembro" class="selectpicker form-control input-sm" opt-value="id" multiple opt-label="ds" data-live-search="true" title="Escolha um ou mais nomes" data-selected-text-format="count > 5" data-width="100%" data-container="body" data-actions-box="true"></select>
		</div>
	</div>
	<div class="col-xs-12 col-md-12">
		<div class="row form-group form-group-sm">
			<input type="button" id="clearSelection" class="btn btn-warning  pull-left" value="Limpar Selecionadas"/>
			<input type="submit" class="btn btn-success pull-right" value="Imprimir Selecionadas"/>
		</div>
	</div>
</form>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/printAutoriz.js<?php echo "?".microtime();?>"></script>