<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Tabela de Unidades</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="comDataTable">
			<thead>
				<tr>
					<th>C&oacute;digo</th>
					<th>Idade</th>
					<th>Nome</th>
					<th>G&ecirc;nero</th>
					<th>Cor</th>
					<th>Ativa</th>
				</tr>
			</thead>
			<tbody/>
		</table>
		<br/>
	</div>
	<?php
	/*
	<div class="row">
		<a role="button" class="btn btn-warning pull-left" id="btnNovo"><i class="fas fa-plus"></i>&nbsp;Novo</a>
	</div>
	*/
	?>
</div>
<div class="modal fade" id="comModal" role="dialog" data-backdrop="static"><!---->
	<div class="modal-dialog modal-lg"><!---->
		<div class="modal-content">
			<div class="modal-body">
				<form method="post" id="cadComForm">
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-warning" aria-expanded="false">
								<div class="panel-heading">
									<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
									<h3 class="panel-title"><b>Unidade</b></h3>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="form-group col-xs-1">
											<label for="id" class="control-label">C&oacute;digo</label>
											<input type="text" name="comID" id="comID" field="id" class="form-control input-sm" placeholder="C&oacute;digo" disabled="disabled" style="text-align:center"/>
										</div>
                    <div class="form-group col-xs-7">
											<label for="name" class="control-label">Nome</label>
											<input type="text" name="comName" id="comName" field="ds" class="form-control input-sm" placeholder="Nome" disabled="disabled"/>
										</div>
                    <div class="form-group col-xs-2">
                      <label for="nrIdade" class="control-label">Idade</label>
                      <input type="text" name="nrIdade" id="nrIdade" field="idade" class="form-control" placeholder="Idade" data-min="10" data-max="15"/>
                    </div>
                    <div class="form-group col-xs-2">
											<label for="fgAtiva" class="control-label">Ativa</label>
											<input type="checkbox" name="fgAtiva" id="fgAtiva" field="fg_ativa" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="success" data-offstyle="default" data-on="<b>SIM</b>" data-off="N&Atilde;O" data-size="small" data-width="80"/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<a role="button" type="submit" id="btnGravar" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</a>
						</div>	
					</div>	
				</form>
			</div>	
		</div>
	</div>
</div>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/tabelaUnidade.js<?php echo "?".microtime();?>"></script>