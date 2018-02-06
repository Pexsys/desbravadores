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
					array( "id" => "X", "ds" => "Sexo", "icon" => "fas fa-venus-mars" ),
					array( "id" => "G", "ds" => "Grupo", "icon" => "fas fa-object-group" ),
					array( "id" => "U", "ds" => "Unidade", "icon" => "fas fa-universal-access" ),
					array( "id" => "T", "ds" => "Membros Ativos", "icon" => "fas fa-toggle-on" ),
					array( "id" => "HT", "ds" => "Tipo de Material", "icon" => "fas fa-shopping-basket" )
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
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/consultaMateriais.js<?php echo "?".microtime();?>"></script>