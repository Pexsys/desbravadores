<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Hist&oacute;rico de Aprendizado</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
		<?php FILTERS::Data(
			array(
				"filterTo" => "#aprHstTable",
				"filters" =>
					array(
						array( "id" => "Z", "ds" => "Tipo", "icon" => "fa fa-check" ),
						array( "id" => "X", "ds" => "Sexo","icon" => "fa fa-venus-mars" ),
						array( "id" => "C", "ds" => "Classe", "icon" => "fa fa-graduation-cap" ),
						array( "id" => "G", "ds" => "Grupo", "icon" => "fa fa-group" ),
						array( "id" => "HH", "ds" => "Avaliações", "icon" => "fa fa-thermometer-half" ),
						array( "id" => "U", "ds" => "Unidade", "icon" => "fa fa-universal-access" ),
						array( "id" => "A", "ds" => "Áreas", "icon" => "fa fa-bookmark" ),
						array( "id" => "T", "ds" => "Membros Ativos", "icon" => "fa fa-toggle-on" ),
						array( "id" => "I", "ds" => "Membros Inativos", "icon" => "fa fa-toggle-off" ),
						array( "id" => "E", "ds" => "Especialidade", "icon" => "fa fa-check-circle-o" ),
						array( "id" => "M", "ds" => "Mestrado", "icon" => "fa fa-check-circle" )
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
</div>
<script src="<?php echo PATTERNS::getVD();?>admin/view/screens/aprendizado/historico/index.js<?php echo "?".time();?>"></script>
