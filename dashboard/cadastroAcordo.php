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
		<h3 class="page-header">Cadastro de Acordo Financeiro</h3>
	</div>
</div>
<div class="col-lg-12" id="divLista">
	<div class="row">
	<?php fDataFilters(
		array(
			"filterTo" => "#comDataTable",
			"filters" =>
				array(
					array( "id" => "SA", "ds" => "Status", "icon" => "far fa-hourglass-start" )
				)
		)
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="comDataTable">
			<thead>
				<tr>
					<th></th>
					<th>C&oacute;digo</th>
					<th>Patrocinador</th>
					<th>Patrocinado</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody/>
		</table>
		<br/>
	</div>
	<div class="row">
		<a role="button" class="btn btn-warning pull-left" id="btnNovo"><i class="far fa-plus"></i>&nbsp;Novo</a>
	</div>
</div>
<div class="col-lg-12" id="divAcordo" style="display:none">
	<div class="row">
		<a role="button" class="btn btn-primary pull-left" id="btnFechar"><i class="far fa-times"></i>&nbsp;Fechar</a>
		<a role="button" class="btn btn-success pull-right" id="btnGravar"><i class="far fa-floppy-o"></i>&nbsp;Salvar</a>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/cadastroAcordo.js<?php echo "?".microtime();?>"></script>
