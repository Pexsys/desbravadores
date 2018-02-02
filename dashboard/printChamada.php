<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Imprimir Chamada</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-3">
		<label for="cbMeses" class="control-label">M&ecirc;s:</label>
		<select name="cbMeses" id="cbMeses" class="selectpicker form-control input-sm" opt-value="id" opt-subtext="sb" opt-search="ds" opt-label="ds" multiple data-live-search="true" title="Escolha um ou mais meses" data-selected-text-format="count > 4" data-width="100%" data-container="body"></select>
	</div>
</div>
<br/>
<div class="row">
	<div class="col-lg-5">
		<label for="cbUnidades" class="control-label">Unidades:</label>
		<select name="cbUnidades" id="cbUnidades" class="selectpicker form-control input-sm" opt-value="id" opt-search="ds" opt-label="ds" multiple data-live-search="true" title="Escolha uma ou mais unidades" data-selected-text-format="count > 2" data-width="100%" data-container="body" data-actions-box="true"></select>
	</div>
</div>
<br/>
<div class="row">
	<div class="col-lg-5">
		<button id="btnGerar" class="btn btn-success pull-right"><i class="far fa-print"></i>&nbsp;Gerar</button>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/printChamada.js<?php echo "?".microtime();?>"></script>