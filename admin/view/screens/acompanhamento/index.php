<style>
.btn-default.active {
    background-color: #337ab7;
    border-color: #2e6da4;
    color: #fff;
}
</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro de Acompanhamento de Requisitos</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
    	<div class="row">
    	<?php FILTERS::Data(
    		array(
    			"filterTo" => "#acompDatatable",
    			"filters" =>
    				array(
    					array( "id" => "HA", "ds" => "Avaliações", "icon" => "fa fa-thermometer-half" ),
    					array( "id" => "X", "ds" => "Sexo", "icon" => "fa fa-venus-mars" ),
    					array( "id" => "C", "ds" => "Classe", "icon" => "fa fa-graduation-cap" ),
    					array( "id" => "G", "ds" => "Grupo", "icon" => "fa fa-group" ),
    					array( "id" => "U", "ds" => "Unidade", "icon" => "fa fa-universal-access" )
    				)
    		)
    	);?>
    	</div>
    	<div class="row">
    		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="acompDatatable">
    			<thead>
    				<tr>
    					<th></th>
    					<th></th>
    					<th>Nome Completo</th>
    					<th>Classe</th>
    					<th>In&iacute;cio</th>
    					<th>Conclus&atilde;o</th>
    					<th>Avalia&ccedil;&atilde;o</th>
    					<th>%</th>
    				</tr>
    			</thead>
    		</table>
    		<br/>
    	</div>
    	<div class="row">
    		<button class="btn btn-primary" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Adicionar</button>
    	</div>
    </div>
    <div class="modal fade" id="acompModal" role="dialog" data-backdrop="static"> <!---->
    	<form method="post" id="cadAcompForm">
    	<div class="modal-dialog"> <!---->
    		<div class="modal-content">
    			<div class="modal-header">
    				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
    			</div>
    			<div class="modal-body">
    				<form method="post" id="cadAprendBarForm">
    					<input type="hidden" id="barOp" field="op"/>
    					<div class="panel panel-warning">
    						<div class="panel-body" style="padding:4px 10px 0px">
    							<div class="row">
    								<div class="form-group col-md-4 col-xs-6 col-sm-6 col-lg-4">
    									<div class="input-group">
    										<div class="input-group-addon"><i class="fa fa-barcode"></i></div>
    										<input type="text" id="cdBar" name="cdBar" field="cd_bar" class="form-control input-sm" placeholder="C&oacute;digo" style="text-transform: uppercase"
    											maxlength="<?php echo PATTERNS::getBars()->getLength();?>"
    											pattern="<?php echo PATTERNS::getBars()->getPattern("A");?>"
    											data-fv-regexp-message="C&oacute;digo inv&aacute;lido"
    										/>
    									</div>
    								</div>
    							</div>
    							<div class="col-lg-12" id="divResultado" style="display:none"></div>
    						</div>
    						<div class="panel-footer">
    							<div class="row">
    								<div class="col-lg-12">
    									<button id="btnGravar" class="btn btn-success pull-right"><i class="glyphicon glyphicon-floppy-save"></i>&nbsp;Gravar</button>
    								</div>
    							</div>
    						</div>
    					</div>
    				</form>
    			</div>
    		</div>
    	</div>
    </div>
</div>
<script src="<?php echo PATTERNS::getVD();?>js/readdata.lib.js<?php echo "?".time();?>"></script>
<script src="<?php echo PATTERNS::getVD();?>admin/view/screens/acompanhamento/index.js<?php echo "?".time();?>"></script>
