<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Consulta de Entrega de Materiais</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
		<?php FILTERS::Data(
			array(
				"filterTo" => "#matHstTable",
				"filters" =>
					array(
						array( "id" => "X", "ds" => "Sexo", "icon" => "fa fa-venus-mars" ),
						array( "id" => "G", "ds" => "Grupo", "icon" => "fa fa-group" ),
						array( "id" => "U", "ds" => "Unidade", "icon" => "fa fa-universal-access" ),
						array( "id" => "T", "ds" => "Membros Ativos", "icon" => "fa fa-toggle-on" ),
						array( "id" => "HT", "ds" => "Tipo de Material", "icon" => "fa fa-shopping-basket" )
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
</div>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/consultaMateriais.js<?php echo "?".microtime();?>"></script>
