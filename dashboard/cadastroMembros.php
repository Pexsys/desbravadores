<?php
@require_once("../include/filters.php");
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Cadastro de Membros</h3>
	</div>
</div>
<div class="col-lg-12">
	<div class="row">
	<?php
	fDataFilters( 
		array( 
			"filterTo" => "#membrosDatatable",
			"filters" => 
				array( 
					array( "value" => "G", "label" => "Grupo" ),
					array( "value" => "X", "label" => "Sexo" ),
					array( "value" => "B", "label" => "Batizado" ),
					array( "value" => "U", "label" => "Unidade" ),
					array( "value" => "PC", "label" => "Pend&ecirc;ncia Cadastral" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="membrosDatatable">
			<thead>
				<tr>
					<th>C&oacute;d.</th>
					<th>Nome Completo</th>
					<th>Unidade</th>
					<th>Cargo</th>
				</tr>
			</thead>
			<tbody/>
		</table>
		<br/>
	</div>
	<div class="row">
		<a role="button" class="btn btn-warning pull-left" id="btnNovo" style="display:none"><i class="fa fa-plus"></i>&nbsp;Novo</a>
		<a role="button" class="btn btn-primary pull-right" name="filtro" id="btnAtivos" tp-filtro="A"><i class="fa fa-child"></i>&nbsp;Ativos</a>
		<a role="button" class="btn btn-primary-outline pull-right" name="filtro" id="btnTodos" tp-filtro="T"><i class="fa fa-globe"></i>&nbsp;Todos</a>
	</div>
</div>
<div class="modal fade" id="membrosModal" role="dialog" data-backdrop="static">
	<div class="modal-dialog"> <!---->
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button" id="btnX">&times;</button>
			</div>			
			<div class="modal-body">
				<form method="post" id="cadMembrosForm">
					<ul class="nav nav-pills">
						<li><a data-toggle="tab" href="#Pessoal">Pessoal</a></li>
						<li id="abaAtribuicoes"><a data-toggle="tab" href="#Atrib">Atribui&ccedil;&otilde;es</a></li>
						<li id="abaEndereco"><a data-toggle="tab" href="#Ender">Endere&ccedil;o e Telefones</a></li>
						<li><a data-toggle="tab" href="#Outros">Outros</a></li>
						<li id="abaResponsavel" style="display:none"><a data-toggle="tab" href="#Resp">Respons&aacute;vel</a></li>
					</ul>
					<hr/>
					<div class="row">
						<div class="form-group col-xs-2">
							<label for="membroID" class="control-label">C&oacute;digo</label>
							<input type="text" name="membroID" id="membroID" field="cad_pessoa-id" class="form-control input-sm" placeholder="ID" disabled="disabled" style="text-align:center"/>
						</div>
						<div class="form-group col-xs-10">
							<label for="nmCompleto" class="control-label">Nome Completo</label>
							<input type="text" name="nmCompleto" id="nmCompleto" field="cad_pessoa-nm" class="form-control input-sm" placeholder="Nome Completo" style="text-transform: uppercase"/>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="tab-content">
							<div id="Pessoal" class="col-lg-12 tab-pane fade">
								<div class="row">
									<div class="form-group col-xs-3">
										<label for="tpSexo" class="control-label">Sexo</label>
										<input type="checkbox" name="tpSexo" id="tpSexo" field="cad_pessoa-tp_sexo" value-on="M" value-off="F" data-toggle="toggle" data-width="110" data-onstyle="info" data-offstyle="danger" data-size="small" data-on="<b>MASCULINO</b>" data-off="<b>FEMINIMO</b>"/>
									</div>
									<div class="form-group col-xs-4">
										<label for="dtNascimento" class="control-label">Data de Nascimento</label>
										<div class="input-group">
											<input type="text" name="dtNascimento" id="dtNascimento" field="cad_pessoa-dt_nasc" class="form-control input-sm date" placeholder="Data Nascimento"/>
										</div>
									</div>
									<div class="form-group col-xs-3">
										<label for="nrIdade" class="control-label">Idade</label>
										<div class="input-group">
											<input type="text" name="nrIdade" id="nrIdade" class="form-control input-sm" style="font-size:18px;color:#ff0000;text-align:center;font-weight:bolder" readonly="readonly"/>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-4">
										<label for="nrDoc" class="control-label">Doc. Identifica&ccedil;&atilde;o</label>
										<input type="text" name="nrDoc" id="nrDoc" field="cad_pessoa-nr_doc" class="form-control input-sm" placeholder="DOC" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-4">
										<label for="nrCPF" class="control-label">CPF</label>
										<input type="text" name="nrCPF" id="nrCPF" field="cad_pessoa-nr_cpf" class="form-control input-sm cpf" placeholder="CPF" style="padding-right:0px"/>
									</div>
								</div>
							</div>
							<div id="Ender" class="col-lg-12 tab-pane fade">
								<div class="row">
									<div class="form-group col-xs-3">
										<label for="dsCEP" class="control-label">CEP</label>
										<input type="text" name="dsCEP" id="dsCEP" field="cad_pessoa-cep" class="form-control input-sm cep" placeholder="CEP"/>
									</div>
									<div class="form-group col-xs-7">
										<label for="dsLogra" class="control-label">Logradouro</label>
										<input type="text" name="dsLogra" id="dsLogra" field="cad_pessoa-logradouro" class="form-control input-sm" placeholder="Logradouro" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-2">
										<label for="nrLog" class="control-label">N&ordm;</label>
										<input type="text" name="nrLog" id="nrLog" field="cad_pessoa-nr_logr" class="form-control input-sm" placeholder="N&ordm;" style="padding-right:0px; uppercase"/>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-6">
										<label for="dsComp" class="control-label">Complemento</label>
										<input type="text" name="dsComp" id="dsComp" field="cad_pessoa-complemento" class="form-control input-sm" placeholder="Complemento" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-6">
										<label for="dsBai" class="control-label">Bairro</label>
										<input type="text" name="dsBai" id="dsBai" field="cad_pessoa-bairro" class="form-control input-sm" placeholder="Bairro" style="text-transform: uppercase"/>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-4">
										<label for="dsCid" class="control-label">Cidade</label>
										<input type="text" name="dsCid" id="dsCid" field="cad_pessoa-cidade" class="form-control input-sm" placeholder="Cidade" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-2">
										<label for="cmUF" class="control-label">UF</label>
										<select field="cad_pessoa-uf" name="cmUF" id="cmUF" class="form-control input-sm" style="padding-right:0px">
											<?php fDomainStatic( array( "table" => "TAB_UF", "id" => "ID", "order" => "ID" ) );?>
										</select>
									</div>
									<div class="form-group col-xs-3">
										<label for="nrFoneRes" class="control-label">Fone Res.</label>
										<input type="text" name="nrFoneRes" id="nrFoneRes" field="cad_pessoa-fone_res" class="form-control input-sm sp_celphones" placeholder="Tel. Residencial"/>
									</div>
									<div class="form-group col-xs-3">
										<label for="nrFoneCel" class="control-label">Fone Cel.</label>
										<input type="text" name="nrFoneCel" id="nrFoneCel" field="cad_pessoa-fone_cel" class="form-control input-sm sp_celphones" placeholder="Celular"/>
									</div>
								</div>
							</div>
							<div id="Resp" class="col-lg-12 tab-pane fade">
								<input type="hidden" field="cad_resp-id" id="respID"/>
								<div class="row">
									<div class="form-group col-xs-3">
										<label for="nrCPFResp" class="control-label">CPF Resp.</label>
										<input type="text" name="nrCPFResp" id="nrCPFResp" field="cad_resp-cpf_resp" class="form-control input-sm cpf" placeholder="CPF" style="padding-right:0px"/>
									</div>
									<div class="form-group col-xs-9">
										<label for="nmResponsavel" class="control-label">Nome Completo do Respons&aacute;vel</label>
										<input type="text" name="nmResponsavel" id="nmResponsavel" field="cad_resp-nm_resp" class="form-control input-sm" placeholder="Nome" style="text-transform: uppercase"/>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-3">
										<label for="dsParentesco" class="control-label">Resp.</label>
										<input type="text" name="dsParentesco" id="dsParentesco" field="cad_resp-ds_resp" class="form-control input-sm" placeholder="Parentesco" style="padding-right:0px" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-2">
										<label for="tpSexoResp" class="control-label">Sexo</label>
										<input type="checkbox" name="tpSexoResp" id="tpSexoResp" field="cad_resp-tp_sexo_resp" value-on="M" value-off="F" data-toggle="toggle" data-width="90" data-onstyle="info" data-offstyle="danger" data-size="small" data-on="Masculino" data-off="Feminino"/>
									</div>
									<div class="form-group col-xs-4">
										<label for="nrDocResp" class="control-label">DOC. Resp.</label>
										<input type="text" name="nrDocResp" id="nrDocResp" field="cad_resp-doc_resp" class="form-control input-sm" placeholder="DOC" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-3">
										<label for="nrFoneResp" class="control-label">Fone Resp.</label>
										<input type="text" name="nrFoneResp" id="nrFoneResp" field="cad_resp-tel_resp" class="form-control input-sm sp_celphones" placeholder="Telefone" style="padding-right:0px"/>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-12">
										<label for="dsEmailResp" class="control-label">Email do Respons&aacute;vel</label>
										<input type="text" name="dsEmailResp" id="dsEmailResp" field="cad_resp-email_resp" class="form-control input-sm" placeholder="E-mail do Respons&aacute;vel" style="text-transform: lowercase"/>
									</div>
								</div>
							</div>
							<div id="Atrib" class="col-lg-12 tab-pane fade">
								<div class="row">
									<div class="form-group col-xs-5">
										<label for="cmUnidade" class="control-label">Unidade Atual</label>
										<select field="cad_ativos-id_unidade" name="cmUnidade" id="cmUnidade" class="form-control input-sm"></select>
									</div>
									<div class="form-group col-xs-7">
										<label for="cmCargo" class="control-label">Cargo/Fun&ccedil;&atilde;o</label>
										<select field="cad_ativos-cd_cargo" name="cmCargo" id="cmCargo" class="form-control input-sm" style="padding-right:0px"></select>
									</div>
								</div>
								<div class="row" id="divCargo2" style="display:none">
									<div class="form-group col-xs-12">
										<label for="cmCargo2" class="control-label">Cargo/Fun&ccedil;&atilde;o secund&aacute;ria (se conselheiro(a))</label>
										<select field="cad_ativos-cd_cargo2" name="cmCargo2" id="cmCargo2" class="form-control input-sm" style="padding-right:0px"></select>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-2">
										<label for="cmCamiseta" class="control-label">Camiseta</label>
										<select field="cad_ativos-tp_camiseta" name="cmCamiseta" id="cmCamiseta" class="form-control input-sm">
											<?php fDomainStatic( array( "table" => "TAB_TAMANHOS", "where" => "TP = 'C'", "id" => "CD", "order" => "ORD" ) );?>
										</select>
									</div>
									<div class="form-group col-xs-2">
										<label for="cmAgasalho" class="control-label">Agasalho</label>
										<select field="cad_ativos-tp_agasalho" name="cmAgasalho" id="cmAgasalho" class="form-control input-sm">
											<?php fDomainStatic( array( "table" => "TAB_TAMANHOS", "where" => "TP = 'A'", "id" => "CD", "order" => "ORD" ) );?>
										</select>
									</div>
									<div class="form-group col-xs-4">
										<label for="cmFanfarra" class="control-label">Instrumento Fanfarra</label>
										<select field="cad_ativos-cd_fanfarra" name="cmFanfarra" id="cmFanfarra" class="form-control input-sm" style="padding-right:0px"></select>
									</div>
									<div class="form-group col-xs-4">
										<label for="fgReuniao" class="control-label">Reuni&atilde;o Semanal</label>
										<input type="checkbox" name="fgReuniao" id="fgReuniao" field="cad_ativos-fg_reu_sem" value-on="S" value-off="N" data-toggle="toggle" data-width="120" data-onstyle="info" data-offstyle="default" data-size="small" data-on="SIM" data-off="N&Atilde;O"/>
									</div>
								</div>
								<div class="row" id="divDiretoria">
									<div class="form-group col-xs-4">
										<label for="cmAnoDir" class="control-label">Ano Diretoria</label>
										<select field="cad_pessoa-ano_dir" name="cmAnoDir" id="cmAnoDir" class="form-control">
											<option value=""></option>
										</select>
									</div>
									<div class="form-group col-xs-4">
										<label for="nrUltEstrela" class="control-label"><i class="fa fa-star-o"></i> (&Uacute;ltima devolvida)</label>
										<input type="text" name="nrUltEstrela" id="nrUltEstrela" field="cad_pessoa-estr_devol" class="form-control" placeholder="&Uacute;ltima Estrela" data-min="2"/>
									</div>
									<div class="form-group col-xs-4">
										<label for="nrUniformes" class="control-label">Qtd. Uniformes</label>
										<input type="text" name="nrUniformes" id="nrUniformes" field="cad_pessoa-qt_uniformes" class="form-control" placeholder="Uniformes" data-min="0" data-max="3"/>
									</div>
								</div>
							</div>
							<div id="Outros" class="col-lg-12 tab-pane fade in active">
								<div class="row">
									<div class="form-group col-xs-12">
										<label for="dsEmail" class="control-label">Email principal</label>
										<input type="text" name="dsEmail" id="dsEmail" field="cad_pessoa-email" class="form-control input-sm" placeholder="E-mail" style="text-transform: lowercase"/>
									</div>
									<div class="form-group col-xs-12">
										<label for="dsInstEns" class="control-label">Nome completo da escola onde estuda</label>
										<input type="text" name="dsInstEns" id="dsInstEns" field="cad_pessoa-nm_escola" class="form-control input-sm" placeholder="Onde Estuda?" style="text-transform: uppercase"/>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-8">
										<label for="dsReligiao" class="control-label">Religi&atilde;o</label>
										<input type="text" name="dsReligiao" id="dsReligiao" field="cad_pessoa-ds_relig" class="form-control input-sm" placeholder="Religi&atilde;o" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-4">
										<label for="dtBatismo" class="control-label">Data de Batismo</label>
										<div class="input-group">
											<input type="text" name="dtBatismo" id="dtBatismo" field="cad_pessoa-dt_bat" class="form-control input-sm date" placeholder="Data Batismo"/>
										</div>
									</div>
								</div>
							</div>
						    <hr/>
						    <div class="form-group col-xs-3 pull-left">
								<input type="text" field="cad_pessoa-bc" class="form-control input-sm" style="font-size:18px;text-align:center;font-weight:bolder" readonly="readonly"/>
							</div>
							<div class="form-group col-xs-3 pull-right">
								<input type="checkbox" name="cbAtivo" id="cbAtivo" field="fg_ativo" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="success" data-offstyle="default" data-on="<b>ATIVO</b>" data-off="INATIVO" data-size="small" data-width="110"/>
							</div>
						</div>
					</div>
				</form>
			</div>	
			<div class="modal-footer">
				<?php /*
				<a role="button" class="btn btn-default pull-right" id="btnAprendizado"><i class="fa fa-graduation-cap"></i></a>
				<a role="button" class="btn btn-default pull-right" id="btnHistorico"><i class="fa fa-university"></i></a>
				<a role="button" class="btn btn-default pull-right" id="btnFinanceiro"><i class="fa fa-money"></i></a>
				*/?>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navFirst" type-nav="first"><i class="glyphicon glyphicon-step-backward"></i></a>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navPrior" type-nav="prior"><i class="glyphicon glyphicon-chevron-left"></i></a>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navNext" type-nav="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navLast" type-nav="last"><i class="glyphicon glyphicon-step-forward"></i></a>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>js/correios.lib.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/cadastroMembros.js<?php echo "?".microtime();?>"></script>