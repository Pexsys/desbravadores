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
          array( "id" => "G", "ds" => "Grupo", "icon" => "fas fa-object-group" ),
          array( "id" => "C", "ds" => "Classe", "icon" => "fas fa-graduation-cap" ),
					array( "id" => "X", "ds" => "Sexo", "icon" => "fas fa-venus-mars" ),
					array( "id" => "B", "ds" => "Batizado", "icon" => "fas fa-bath" ),
					array( "id" => "U", "ds" => "Unidade", "icon" => "fas fa-universal-access" ),
					array( "id" => "RG", "ds" => "Regime Alimentar", "icon" => "fas fa-utensils" ),
          array( "id" => "RE", "ds" => "Restrição Alimentar", "icon" => "fas fa-utensils" ),
					array( "id" => "PC", "ds" => "Pend&ecirc;ncia Cadastral", "icon" => "fas fa-exclamation-triangle" )
				)
		) 
	);?>
	</div>
	<div class="row">
		<table class="compact row-border hover stripe" style="cursor: pointer;" cellspacing="0" width="100%" id="membrosDatatable">
			<thead>
				<tr>
					<th></th>
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
		<a role="button" class="btn btn-warning pull-left" id="btnNovo" style="display:none"><i class="fas fa-plus"></i>&nbsp;Novo</a>
		<a role="button" class="btn btn-primary pull-right" name="filtro" id="btnAtivos" tp-filtro="A"><i class="fas fa-filter"></i>&nbsp;Ativos</a>
		<a role="button" class="btn btn-primary-outline pull-right" name="filtro" id="btnTodos" tp-filtro="T"><i class="fas fa-globe"></i>&nbsp;Todos</a>
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
						<li id="abaEndereco"><a data-toggle="tab" href="#Ender">Endere&ccedil;o e Telefones</a></li>
						<li><a data-toggle="tab" href="#Outros">Outros</a></li>
						<li id="abaResponsavel" style="display:none"><a data-toggle="tab" href="#Resp">Respons&aacute;vel</a></li>
						<li id="abaAtribuicoes"><a data-toggle="tab" href="#Atrib">Atribui&ccedil;&otilde;es</a></li>
					</ul>
					<hr style="margin:10px"/>
					<div class="row">
						<div class="form-group col-xs-2">
							<input type="hidden" name="membroID" id="membroID" field="cad_membro-id"/>
							<label for="id_membro" class="control-label">C&oacute;digo</label>
							<input type="text" name="id_membro" id="id_membro" field="cad_membro-id_membro" class="form-control input-sm" placeholder="ID" disabled="disabled" style="text-align:center"/>
						</div>
						<div class="form-group col-xs-10">
							<label for="nmCompleto" class="control-label">Nome Completo</label>
							<input type="text" name="nmCompleto" id="nmCompleto" field="cad_pessoa-nm" class="form-control input-sm" placeholder="Nome Completo" style="text-transform: uppercase"/>
						</div>
					</div>
					<hr style="margin:1px"/>
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
									<div class="form-group col-xs-5">
										<label for="nrCPF" class="control-label">CPF</label>
										<input type="text" name="nrCPF" id="nrCPF" field="cad_pessoa-nr_cpf" class="form-control input-sm cpf" placeholder="CPF" style="padding-right:0px"/>
									</div>
								</div>
                <div class="row">
                  <div class="form-group col-xs-6">
										<label for="cmTabRegAlim" class="control-label">Regime Alimentar</label>
										<select field="cad_pessoa-id_tab_tp_reg_alim" name="cmTabRegAlim" id="cmTabRegAlim" class="form-control input-sm">
                    <?php fDomainStatic( array( "table" => "TAB_TP_REG_ALIM", "id" => "ID", "ds" => "DS", "order" => "DS" ) );?>
                    </select>
									</div>
                  <div class="form-group col-xs-6">
										<label for="cmTabRestAlim" class="control-label">Restrição Alimentar</label>
										<select field="cad_pessoa-id_tab_tp_rest_alim" name="cmTabRestAlim" id="cmTabRestAlim" class="form-control input-sm">
                    <?php fDomainStatic( array( "table" => "TAB_TP_REST_ALIM", "id" => "ID", "ds" => "DS", "order" => "DS" ) );?>
                    </select>
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
										<input type="text" name="nrLog" id="nrLog" field="cad_pessoa-nr_logr" class="form-control input-sm" placeholder="N&ordm;" style="padding-right:0px; text-transform: uppercase"/>
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
								<input type="hidden" field="cad_resp_legal-id_cad_pessoa" id="respID"/>
								<div class="row">
									<div class="form-group col-xs-3">
										<label for="nrCPFResp" class="control-label">CPF Resp.</label>
										<input type="text" name="nrCPFResp" id="nrCPFResp" field="cad_resp_legal-nr_cpf" class="form-control input-sm cpf" placeholder="CPF" style="padding-right:0px"/>
									</div>
									<div class="form-group col-xs-9">
										<label for="nmResponsavel" class="control-label">Nome Completo do Respons&aacute;vel</label>
										<input type="text" name="nmResponsavel" id="nmResponsavel" field="cad_resp_legal-nm" class="form-control input-sm" placeholder="Nome" style="text-transform: uppercase"/>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-3">
										<label for="dsParentesco" class="control-label">Resp.</label>
										<input type="text" name="dsParentesco" id="dsParentesco" field="cad_resp_legal-ds_tp" class="form-control input-sm" placeholder="Parentesco" style="padding-right:0px" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-2">
										<label for="tpSexoResp" class="control-label">Sexo</label>
										<input type="checkbox" name="tpSexoResp" id="tpSexoResp" field="cad_resp_legal-tp_sexo" value-on="M" value-off="F" data-toggle="toggle" data-width="90" data-onstyle="info" data-offstyle="danger" data-size="small" data-on="Masculino" data-off="Feminino"/>
									</div>
									<div class="form-group col-xs-4">
										<label for="nrDocResp" class="control-label">DOC. Resp.</label>
										<input type="text" name="nrDocResp" id="nrDocResp" field="cad_resp_legal-nr_doc" class="form-control input-sm" placeholder="DOC" style="text-transform: uppercase"/>
									</div>
									<div class="form-group col-xs-3">
										<label for="nrFoneResp" class="control-label">Fone Resp.</label>
										<input type="text" name="nrFoneResp" id="nrFoneResp" field="cad_resp_legal-fone_cel" class="form-control input-sm sp_celphones" placeholder="Telefone" style="padding-right:0px"/>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-12">
										<label for="dsEmailResp" class="control-label">Email do Respons&aacute;vel</label>
										<input type="text" name="dsEmailResp" id="dsEmailResp" field="cad_resp_legal-email" class="form-control input-sm" placeholder="E-mail do Respons&aacute;vel" style="text-transform: lowercase"/>
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
										<select field="cad_membro-ano_dir" name="cmAnoDir" id="cmAnoDir" class="form-control">
											<option value=""></option>
										</select>
									</div>
									<div class="form-group col-xs-4">
										<label for="nrUltEstrela" class="control-label"><i class="far fa-star"></i> (&Uacute;ltima devolvida)</label>
										<input type="text" name="nrUltEstrela" id="nrUltEstrela" field="cad_membro-estr_devol" class="form-control" placeholder="&Uacute;ltima Estrela" data-min="2"/>
									</div>
									<div class="form-group col-xs-4">
										<label for="nrUniformes" class="control-label">Qtd. Uniformes</label>
										<input type="text" name="nrUniformes" id="nrUniformes" field="cad_membro-qt_uniformes" class="form-control" placeholder="Uniformes" data-min="0" data-max="3"/>
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
				<a role="button" class="btn btn-default pull-right" id="btnAprendizado"><i class="fas fa-graduation-cap"></i></a>
				<a role="button" class="btn btn-default pull-right" id="btnHistorico"><i class="fas fa-university"></i></a>
				<a role="button" class="btn btn-default pull-right" id="btnFinanceiro"><i class="fas fa-money"></i></a>
				*/?>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navFirst" type-nav="first"><i class="glyphicon glyphicon-step-backward"></i></a>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navPrior" type-nav="prior"><i class="glyphicon glyphicon-chevron-left"></i></a>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navNext" type-nav="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
				<a role="button" class="btn btn-default pull-left" name="memberNavigate" id="navLast" type-nav="last"><i class="glyphicon glyphicon-step-forward"></i></a>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo PATTERNS::getVD();?>js/correios.lib.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/cadastroMembros.js<?php echo "?".microtime();?>"></script>
