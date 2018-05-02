<?php
  @require_once("../include/filters.php");
  ?>
<style>
  .btn-default.active {
    background-color: #337ab7;
    border-color: #2e6da4;
    color: #fff;
  }
</style>
<div class="row">
  <div class="col-lg-12">
    <h3 class="page-header">Cadastro de Acordo Financeiro</h3>
  </div>
</div>
<div class="row" id="divLista">
  <div class="col-lg-12">
    <div class="row">
      <?php
        fDataFilters( 
        	array( 
        		"filterTo" => "#comDataTable",
        		"filters" => 
        			array( 
        				array( "id" => "SA", "ds" => "Status", "icon" => "fa fa-hourglass-start" )
        			)
        	) 
        );?>
    </div>
    <div class="row">
      <table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="comDataTable">
        <thead>
          <tr>
            <th></th>
            <th>C&oacute;digo</th>
            <th>Patrocinador</th>
            <th>Patrocinado</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody/> 
      </table>
      <br/> 
    </div>
    <div class="row"> 
      <a role="button" class="btn btn-primary pull-left" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Novo</a> 
    </div>
  </div>
</div>
<div class="row" id="divAcordo" style="display:none">
  <div class="col-lg-12">
    <div class="panel panel-primary" aria-expanded="false">
      <div class="panel-heading">
        <h3 class="panel-title">Acordo</h3>
      </div>
      <div class="panel-body">
        <div class="col-lg-12">
          <div class="row">
            <hr/>
            <a role="button" class="btn btn-primary pull-left" id="btnFechar"><i class="fas fa-times"></i>&nbsp;Fechar</a>
            <a role="button" class="btn btn-success pull-right" id="btnGravar"><i class="fas fa-save"></i>&nbsp;Salvar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/tesourariaAcordo.js<?php echo "?".time();?>"></script>