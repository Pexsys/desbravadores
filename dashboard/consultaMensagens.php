<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Consulta de Mensagens Emitidas</h3>
	</div>
</div>
<div class="col-lg-12">
  <div class="row">	
	<?php fDataFilters( 
		array( 
			"filterTo" => "#msgDataTable",
			"filters" => 
				array( 
          array( "id" => "TM", "ds" => "Tipo", "icon" => "fas fa-mail-bulk" ),
          array( "id" => "M", "ds" => "Mestrado", "icon" => "fas fa-check-circle" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="msgDataTable">
			<thead>
				<tr>
					<th>Tipo</th>
          <th>Usu&aacute;rio</th>
          <th>Destino</th>
					<th>Gerado</th>
					<th>Enviado</th>
          <th>Lido</th>
				</tr>
			</thead>
			<tbody/>
		</table>
		<br/>
	</div>
</div>
<div class="modal fade" id="comModal" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-warning" aria-expanded="false">
							<div class="panel-heading">
								<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
								<h3 class="panel-title" id="comunicadoTitle"></h3>
							</div>
							<div class="panel-body" id="comunicadoBody" style="height:350px;overflow-y:auto;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/consultaMensagens.js<?php echo "?".microtime();?>"></script>