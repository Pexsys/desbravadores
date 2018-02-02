<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro de Hist&oacute;rico de Aprendizado</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">	
	<?php fDataFilters( 
		array( 
			"filterTo" => "#aprHstTable",
			"filters" => 
				array( 
					array( "id" => "Z", "ds" => "Tipo", "icon" => "far fa-check" ),
					array( "id" => "X", "ds" => "Sexo", "icon" => "far fa-venus-mars" ),
					array( "id" => "C", "ds" => "Classe", "icon" => "far fa-graduation-cap" ),
					array( "id" => "G", "ds" => "Grupo", "icon" => "far fa-group" ),
					array( "id" => "HH", "ds" => "Avaliações", "icon" => "far fa-thermometer-half" ),
					array( "id" => "U", "ds" => "Unidade", "icon" => "far fa-universal-access" ),
					array( "id" => "A", "ds" => "Áreas", "icon" => "far fa-bookmark" ),
					array( "id" => "T", "ds" => "Membros Ativos", "icon" => "fas fa-toggle-on" ),
					array( "id" => "E", "ds" => "Especialidade", "icon" => "far fa-check-circle-o" ),
					array( "id" => "M", "ds" => "Mestrado", "icon" => "far fa-check-circle" )
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
	<div class="row">
		<button class="btn btn-danger" id="btnDelHist"><i class="far fa-trash-o"></i>&nbsp;Excluir</button>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/cadastroAprendizadoHist.js<?php echo "?".microtime();?>"></script>