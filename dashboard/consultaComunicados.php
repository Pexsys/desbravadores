<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Consulta de Comunicados Emitidos</h3>
	</div>
</div>
<div class="col-lg-8">
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="comDataTable">
			<thead>
				<tr>
					<th>Sequ&ecirc;ncia</th>
					<th>C&oacute;digo</th>
					<th>Data de Emiss&atilde;o</th>
					<th>Status</th>
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
				<div class="row">
					<div class="col-lg-12">
						<a role="button" class="btn btn-info pull-right" id="btnCiente" style="display:none"><i class="far fa-check"></i>&nbsp;Eu Li</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/consultaComunicados.js<?php echo "?".microtime();?>"></script>