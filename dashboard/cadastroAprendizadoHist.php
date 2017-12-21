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
					array( "id" => "Z", "ds" => "Tipo" ),
					array( "id" => "X", "ds" => "Sexo" ),
					array( "id" => "C", "ds" => "Classe" ),
					array( "id" => "G", "ds" => "Grupo" ),
					array( "id" => "HH", "ds" => "Avaliações" ),
					array( "id" => "U", "ds" => "Unidade" ),
					array( "id" => "A", "ds" => "&Aacute;reas" ),
					array( "id" => "T", "ds" => "Membros Ativos" ),
					array( "id" => "E", "ds" => "Especialidade" ),
					array( "id" => "M", "ds" => "Mestrado" )
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
		<button class="btn btn-danger" id="btnDelHist"><i class="fa fa-trash-o"></i>&nbsp;Excluir</button>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/cadastroAprendizadoHist.js<?php echo "?".microtime();?>"></script>