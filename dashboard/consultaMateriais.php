<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Consulta de Entrega de Materiais</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">	
	<?php fDataFilters( 
		array( 
			"filterTo" => "#matHstTable",
			"filters" => 
				array( 
					array( "value" => "X", "label" => "Sexo" ),
					array( "value" => "G", "label" => "Grupo" ),
					array( "value" => "U", "label" => "Unidade" ),
					array( "value" => "T", "label" => "Membros Ativos" ),
					array( "value" => "HT", "label" => "Tipo de Material" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="matHstTable">
			<thead>
				<tr>
					<th></th>
					<th>Nome Completo</th>
					<th>Tipo</th>
					<th>Item</th>
					<th>Dt.Entrega</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/consultaMateriais.js<?php echo "?".microtime();?>"></script>