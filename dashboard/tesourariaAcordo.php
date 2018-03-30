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
          <div class="row acc-wizard">
            <div class="col-lg-2 col-md-2" style="padding:0px">
              <ol class="acc-wizard-sidebar"><!--acc-wizard-completed acc-wizard-active-->
                <li class="acc-wizard-todo"><a href="#Patrocinador">Patrocinador</a></li>
                <!--
                <li class="acc-wizard-todo"><a href="#Gerais">Gerais</a></li>
                <li class="acc-wizard-todo"><a href="#Saidas">Saídas</a></li>
                <li class="acc-wizard-todo"><a href="#Uniformes">Uniformes</a></li>
                <li class="acc-wizard-todo"><a href="#Anteriores">2017</a></li>
                -->
              </ol>
            </div>
            <div class="col-lg-10 col-md-10" style="padding:0px">
              <div id="acc-acordo" class="panel-group">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title"> 
                      <a href="#Patrocinador" data-parent="#acc-acordo" data-toggle="collapse" class="">Patrocinador</a> 
                    </h4>
                  </div>
                  <div id="Patrocinador" class="panel-collapse collapse" style="height: auto;">
                    <div class="panel-body">
                      Conteúdo Patrocinador
                      <div class="acc-wizard-step">
                        <button class="btn btn-primary pull-right" type="submit"><i class="fas fa-arrow-alt-circle-right"></i>&nbsp;Próximo</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!--
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title"> 
                      <a href="#Gerais" data-parent="#acc-acordo" data-toggle="collapse" class="">Custos Gerais</a> 
                    </h4>
                  </div>
                  <div id="Gerais" class="panel-collapse collapse" style="height: auto;">
                    <div class="panel-body">
                      Conteúdo Custos Gerais
                      <div class="acc-wizard-step">
                        <button class="btn btn-default pull-left" type="reset"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Anterior</button>
                        <button class="btn btn-primary pull-right" type="submit"><i class="fas fa-arrow-alt-circle-right"></i>&nbsp;Próximo</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title"> 
                      <a href="#Saidas" data-parent="#acc-acordo" data-toggle="collapse" class="">Custos Saídas/Passeios</a> 
                    </h4>
                  </div>
                  <div id="Saidas" class="panel-collapse collapse" style="height: auto;">
                    <div class="panel-body">
                      Conteúdo Saídas/Passeios
                      <div class="acc-wizard-step">
                        <button class="btn btn-default pull-left" type="reset"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Anterior</button>
                        <button class="btn btn-primary pull-right" type="submit"><i class="fas fa-arrow-alt-circle-right"></i>&nbsp;Próximo</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title"> 
                      <a href="#Uniformes" data-parent="#acc-acordo" data-toggle="collapse" class="">Custos de Uniformes</a> 
                    </h4>
                  </div>
                  <div id="Uniformes" class="panel-collapse collapse" style="height: auto;">
                    <div class="panel-body">
                      Conteúdo Uniformes
                      <div class="acc-wizard-step">
                        <button class="btn btn-default pull-left" type="reset"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Anterior</button>
                        <button class="btn btn-primary pull-right" type="submit"><i class="fas fa-arrow-alt-circle-right"></i>&nbsp;Próximo</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title"> 
                      <a href="#Anteriores" data-parent="#acc-acordo" data-toggle="collapse" class="">Débitos Anteriores</a> 
                    </h4>
                  </div>
                  <div id="Anteriores" class="panel-collapse collapse" style="height: auto;">
                    <div class="panel-body">
                      Conteúdos Anteriores
                      <div class="acc-wizard-step">
                        <button class="btn btn-default pull-left" type="reset"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Anterior</button>
                      </div>
                    </div>
                  </div>
                </div>
                -->
              </div> 
            </div> 
          </div>
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
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/tesourariaAcordo.js<?php echo "?".time();?>"></script>