<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Consulta de Aniversariantes</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
	<?php fDataFilters( 
		array( 
			"filterTo" => "#birthTable",
			"filters" => 
				array( 
					array( "id" => "X", "ds" => "Sexo" ),
					array( "id" => "C", "ds" => "Classe" ),
					array( "id" => "G", "ds" => "Grupo" ),
					array( "id" => "MA", "ds" => "Mês de Aniversário" ),
					array( "id" => "U", "ds" => "Unidade" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="birthTable">
			<thead>
				<tr>
					<th></th>
					<th>Nome Completo</th>
					<th>Unidade</th>
					<th>Dia/Mes</th>
					<th>Idade</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/consultaAniversarios.js<?php echo "?".microtime();?>"></script>