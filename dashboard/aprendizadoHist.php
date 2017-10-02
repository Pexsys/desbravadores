<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Hist&oacute;rico de Aprendizado</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">	
	<?php fDataFilters( 
		array( 
			"filterTo" => "#aprHstTable",
			"filters" => 
				array( 
					array( "value" => "Z", "label" => "Tipo" ),
					array( "value" => "X", "label" => "Sexo" ),
					array( "value" => "C", "label" => "Classe" ),
					array( "value" => "G", "label" => "Grupo" ),
					array( "value" => "HH", "label" => "Avaliações" ),
					array( "value" => "U", "label" => "Unidade" ),
					array( "value" => "A", "label" => "&Aacute;reas" ),
					array( "value" => "T", "label" => "Membros Ativos" ),
					array( "value" => "I", "label" => "Membros Inativos" ),
					array( "value" => "E", "label" => "Especialidade" ),
					array( "value" => "M", "label" => "Mestrado" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="aprHstTable">
			<thead>
				<tr>
					<th></th>
					<th>Nome Completo</th>
					<th>Tipo</th>
					<th>Item</th>
					<th>P&aacute;g.</th>
					<th>Dt.Aval.</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/cadastroAprendizadoHist.js<?php echo "?".microtime();?>"></script>