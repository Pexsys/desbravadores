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
      <a role="button" class="btn btn-primary pull-left" id="btnNovo"><i class="fa fa-plus"></i>&nbsp;Novo</a> 
  </div>
</div>
<div class="row" id="divAcordo"> <!--style="display:none"-->
  <div class="col-lg-12">
    <div class="panel panel-primary" aria-expanded="false">
      <div class="panel-heading">
        <h3 class="panel-title">Acordo</h3>
      </div>
      <div class="panel-body">
        <div class="panel-group" id="accAcordo" role="tabpanel" aria-multiselectable="true">
          <div class="panel panel-danger">
            <div class="panel-heading" role="tab" id="headingAcordo">
              <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accAcordo" href="#patrocinador" aria-expanded="true" aria-controls="patrocinador">Responsável Financeiro / Patrocinador</a>
              </h4>
            </div>
            <div id="patrocinador" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingAcordo">
              <div class="panel-body">
                <form method="post" id="patrForm">
                  <div class="row">
                    <div class="form-group col-lg-2">
                      <label for="nrCPFPatr" class="control-label">CPF</label>
                      <input type="text" name="nrCPFPatr" id="nrCPFPatr" field="cad_pessoa-nr_cpf" class="form-control input-sm cpf" placeholder="CPF" style="padding-right:0px"/>
                    </div>
                    <div class="form-group col-lg-6">
                      <label for="nmCompletoPatr" class="control-label">Nome Completo</label>
                      <input type="text" name="nmCompletoPatr" id="nmCompletoPatr" field="cad_pessoa-nm" class="form-control input-sm typeahead" placeholder="Nome Completo" style="text-transform: uppercase"/>
                    </div>
                    <div class="form-group col-lg-2">
                      <label for="dtNascPatr" class="control-label">Data de Nascimento</label>
                      <div class="input-group">
                        <input type="text" name="dtNascPatr" id="dtNascPatr" field="cad_pessoa-dt_nasc" class="form-control input-sm date" placeholder="Data Nascimento"/>
                      </div>
                    </div>
                    <div class="form-group col-lg-2">
                      <label for="tpSexo" class="control-label">Sexo</label>
                      <div class="input-group">
                        <input type="checkbox" name="tpSexo" id="tpSexo" field="cad_pessoa-tp_sexo" value-on="M" value-off="F" data-toggle="toggle" data-width="110" data-onstyle="info" data-offstyle="danger" data-size="small" data-on="<b>MASCULINO</b>" data-off="<b>FEMINIMO</b>"/>
                      </div>
                    </div>
                    <div class="form-group col-lg-8">
                      <label for="dsEmailPatr" class="control-label">Email do Respons&aacute;vel</label>
                      <input type="text" name="dsEmailPatr" id="dsEmailPatr" field="cad_pessoa-email" class="form-control input-sm" placeholder="E-mail do Respons&aacute;vel Financeiro" style="text-transform: lowercase"/>
                    </div>
                    <div class="form-group col-lg-2">
                      <label for="nrFonePatr" class="control-label">Telefone</label>
                      <input type="text" name="nrFonePatr" id="nrFonePatr" field="cad_pessoa-fone_cel" class="form-control input-sm sp_celphones" placeholder="Telefone"/>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="panel panel-danger" id="divAcordoMembros"> <!-- style="display:none"-->
            <div class="panel-heading" role="tab" id="headingMembros">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accAcordo" href="#accAcordoMembros" aria-expanded="false" aria-controls="accAcordoMembros">Membros / Beneficiados</a>
              </h4>
            </div>
            <div id="accAcordoMembros" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingMembros">
              <div class="panel-body">
                <form method="post" id="mbForm">
                  <div class="col-lg-12">
                    <div class="row">
                      <div class="form-group col-lg-2">
                        <label class="control-label">CPF</label>
                      </div>
                      <div class="form-group col-lg-7">
                        <label class="control-label">Nome Completo</label>
                      </div>
                      <div class="form-group col-lg-2">
                        <label class="control-label">Data de Nascimento</label>
                      </div>
                      <div class="form-group col-lg-1">
                        &nbsp;
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-lg-2">
                        <input type="text" name="mb[0].cpf" id="mb0cpf" field="cad_pessoa-nr_cpf" class="form-control input-sm cpf" placeholder="CPF" style="padding-right:0px"/>
                      </div>
                      <div class="form-group col-lg-7">
                        <input type="text" name="mb[0].name" id="mb0name" field="cad_pessoa-nm" class="form-control input-sm" placeholder="Nome Completo" style="text-transform: uppercase"/>
                      </div>
                      <div class="form-group col-lg-2">
                        <input type="text" name="mb[0].date" id="mb0date" field="cad_pessoa-dt_nasc" class="form-control input-sm date" placeholder="Data Nascimento"/>
                      </div>
                      <div class="form-group col-lg-1">
                        <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>
                    <div class="row hide" id="benefitTemplate">
                      <div class="form-group col-lg-2">
                        <input type="text" field="cad_pessoa-nr_cpf" class="form-control input-sm cpf" placeholder="CPF" style="padding-right:0px"/>
                      </div>
                      <div class="form-group col-lg-7">
                        <input type="text" field="cad_pessoa-nm" class="form-control input-sm" placeholder="Nome Completo" style="text-transform: uppercase"/>
                      </div>
                      <div class="form-group col-lg-2">
                        <input type="text" field="cad_pessoa-dt_nasc" class="form-control input-sm date" placeholder="Data Nascimento"/>
                      </div>
                      <div class="form-group col-lg-1">
                        <button type="button" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="panel panel-danger" id="divAcordoFinanceiro"> <!-- style="display:none"-->
            <div class="panel-heading" role="tab" id="headingFinanceiro">
              <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accAcordo" href="#accAcordoFinanceiro" aria-expanded="false" aria-controls="accAcordoFinanceiro">Financeiro</a>
              </h4>
            </div>
            <div id="accAcordoFinanceiro" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFinanceiro">
              <div class="panel-body">
                <form method="post" id="mbFinanc">
                </form>
              </div>
            </div>
          </div>
        </div>
        <hr/>
        <div class="row">
          <div class="col-lg-3">
            <a role="button" class="btn btn-primary pull-left" id="btnFechar"><i class="fas fa-times"></i>&nbsp;Fechar Acordo</a>
          </div>
          <div class="col-lg-6 text-center">
            <a role="button" class="btn btn-default" id="btnImprimir"><i class="fas fa-print"></i>&nbsp;Imprimir Acordo</a>
          </div>
          <div class="col-lg-3">
            <a role="button" class="btn btn-success pull-right" id="btnGravar"><i class="fas fa-save"></i>&nbsp;Salvar Acordo</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/tesourariaAcordo.js<?php echo "?".time();?>"></script>