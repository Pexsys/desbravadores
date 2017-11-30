var dataTable = undefined;
var tpFiltro = (jsLIB.parameters.flt === 'ALL' ? 'T' : 'A');
var formPopulated = false;
var rowSelected = undefined;

$(document).ready(function(){
	
	dataTable = $('#membrosDatatable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ membros",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type: "POST",
			url: jsLIB.rootDir+"rules/membros.php",
			data: function (d) {
				d.MethodName = "getMembros",
				d.data = { 
					filtro: tpFiltro,
					filters: jsFilter.jSON()
				}
			},
			dataSrc: "membros"
		},
		order: [ 1, 'asc' ],
		columns: [
			{	data: 'id',
				sortable: true,
				width: "5%"
			},
			{	data: 'nm',
				sortable: true,
				type: 'ptbr-string',
				width: "45%"
			},
			{	data: 'uni',
				sortable: true,
				type: 'ptbr-string',
				width: "20%"
			},
			{	data: 'cgo',
				sortable: true,
				type: 'ptbr-string',
				width: "30%"
			}
		]
	})
	.on( 'search.dt', function() {
		ruleBtnNovo();
	});
		
	$('#btnAtivos, #btnTodos').click(function(){
		 switchSelecion( $(this).attr('tp-filtro') );
	});
	
	$('#btnNovo').click(function(){
		fAbaFirstFocus();
		ruleButtons();
		formPopulated = false;
		jsLIB.resetForm( $("#cadMembrosForm") );
		$("#membroID").val("Novo");
		formPopulated = true;
		$("#membrosModal").modal();
	});

	$("#cadMembrosForm")
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})
		.on('err.field.fv', function(e, data) {
			data.element.attr('valid','not-ok');
		})
		.on('success.field.fv', function(e, data) {
			data.element.attr('valid','ok');
		})
		.on('init.field.fv', function(e, data) {
			if (data.element.attr('type') == 'checkbox' ) {
				data.element.attr('valid','ok');
			} else {
				data.element.attr('valid','not-ok');
			}
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
				nmCompleto:		{validators: {
						notEmpty: {
							message: 'O nome completo &eacute; obrigat&oacute;rio'
						},
						regexp: {
							regexp: /^([a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{2,})+(?:\s[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{1,})+$/,
							message: 'Digite no m&iacute;nimo o nome e sobrenome sem espa&ccedil;os no final'
						}
				}},
				nrCPF:			{validators: {
					id: {
						country: 'BR',
						message: 'CPF inv&aacute;lido'
					}
				}},
				nrDoc:			{validators: {
					notEmpty: {
						message: 'Documento obrigat&oacute;rio'
					},
					regexp: {
						regexp: /^([a-zA-Z]{2,})\s([A-Za-z0-9\.\-]{5,}\b)$/,
						message: 'Digite Tipo e Documento'
					}
				}},
				dsLogra:		{validators: {
					notEmpty: {
						message: 'Digite o logradouro'
					},
					regexp: {
						regexp: /^([a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{2,})+(?:\s[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{1,})+$/,
						message: 'Digite no m&iacute;nimo dois nomes sem espa&ccedil;os no final'
					}
				}},
				nrLog:			{validators: {
					notEmpty: {
						message: 'O n&uacute;mero não pode ficar em branco'
					}
				}},
				dsBai:			{validators: {
					notEmpty: {
						message: 'Digite o bairro'
					}
				}},
				dsCid:			{validators: {
					notEmpty: {
						message: 'Digite a cidade'
					}
				}},
				cmUF:			{validators: {
					notEmpty: {
						message: 'Seleciona a UF'
					}
				}},
				dsCEP:			{validators: {
					notEmpty: {
						message: 'Digite o CEP'
					}
				}},
				dsParentesco:	{validators: {
					notEmpty: {
						message: 'Parentesco obrigat&oacute;rio'
					}
				}},
				nmResponsavel:	{validators: {
					notEmpty: {
						message: 'O nome completo &eacute; obrigat&oacute;rio'
					},
					regexp: {
						regexp: /^([a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{2,})+(?:\s[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{1,})+$/,
						message: 'Digite no m&iacute;nimo o nome e sobrenome sem espa&ccedil;os no final'
					}
				}},
				nrDocResp:		{validators: {
					notEmpty: {
						message: 'Documento obrigat&oacute;rio'
					},
					regexp: {
						regexp: /^([a-zA-Z]{2,})\s([A-Za-z0-9\.\-]{5,}\b)$/,
						message: 'Digite Tipo e Documento'
					}
				}},
				nrCPFResp:		{validators: {
					id: {
						country: 'BR',
						message: 'CPF inv&aacute;lido'
					}
				}},
				nrFoneResp:		{validators: {
					notEmpty: {
						message: 'Telefone obrigat&oacute;rio'
					}
				}},
				cmUnidade:		{validators: {
					notEmpty: {
						message: 'Selecione a unidade'
					}
				}},
				cmCargo:		{validators: {
					notEmpty: {
						message: 'Selecione o cargo/fun&ccedil;&atilde;o'
					}
				}},
				cmCamiseta:		{validators: {
					notEmpty: {
						message: 'Selecione a camiseta'
					}
				}},
				cmAgasalho:		{validators: {
					notEmpty: {
						message: 'Selecione o agasalho'
					}
				}},
				dsEmail:		{validators: {
					regexp: {
						regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
						message: 'Formato de email inv&aacute;lido'
					}
				}},
				dsEmailResp:	{validators: {
					regexp: {
						regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
						message: 'Formato de email inv&aacute;lido'
					}
				}},
				dsInstEns:		{validators: {
					regexp: {
						regexp: /^((\b[a-zA-Z\.áàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\']{1,})\s*){1,}$/,
						message: 'Digite a onde estuda'
					}
				}},
				dtNascimento:	{validators: {
					excluded: false,
					notEmpty: {
						message: 'A data de nascimento n&atilde;o pode ser vazia'
					},
					date: {
						format: 'DD/MM/YYYY',
						message: 'Data de nascimento inv&aacute;lida!'
					}
				}},
				dtBatismo:		{validators: {
					excluded: false,
					date: {
						format: 'DD/MM/YYYY',
						message: 'Data de batismo inv&aacute;lida!'
					}
				}},
				tpSexo:			{validators: {}},
				dsComp:			{validators: {}},
				fgReuniao:		{validators: {}},
				nrFoneRes:		{validators: {}},
				nrFoneCel:		{validators: {}},
				tpSexoResp:		{validators: {}},
				cmCargo2:		{validators: {}},
				cmFanfarra:		{validators: {}},
				cmAnoDir:		{validators: {}},
				nrUltEstrela:	{validators: {}},
				nrUniformes:	{validators: {}},
				dsReligiao:		{validators: {}},
				cbAtivo:		{validators: {}}
			}
		})
		.on("change", "[field]", function(e) {
			$("#cadMembrosForm")
				.formValidation('revalidateField', this.id);
			
			if (formPopulated) {
				var membroID = $("#membroID").val();
				var input = $(this);
				var field = input.attr('field');
				var value = jsLIB.getValueFromField(input);
				//console.log(input.attr("id")+": field["+field+":"+value+"]");

				if (field && (input.attr('valid') == 'ok' || field == "cad_pessoa-qt_uniformes") ) {
					formPopulated = false;
					if (field == "cad_pessoa-email" || field == "cad_resp-email_resp" ){
						value = value.toLowerCase();
					}else{
						value = value.toUpperCase();
					}
					input.val(value);
					formPopulated = true;
					
					if (membroID == 'Novo'){
						var nome = jsLIB.getValueFromField($("#nmCompleto"));
						var datn = jsLIB.getValueFromField($("#dtNascimento"));
						var sexo = jsLIB.getValueFromField($("#tpSexo"));
						var doc = jsLIB.getValueFromField($("#nrDoc"));

						if ( doc != "" ) {
							var parameters = {
								id: membroID,
								nm: nome,
								dt: datn,
								sx: sexo,
								dc: doc
							}
							jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/membros.php", { MethodName : 'insertMember', data : parameters }, function(mb){
								if (mb.result == true){
									populateMember(mb);
								}
							});
						}
						
					//se mudou procura, se nao encontrar cpf, insere. se encontrar, retorna dados e popula.
					} else if (field == "cad_resp-cpf_resp") {
						var parameters = {
							id	: membroID,
							cpf	: value
						}
						jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/membros.php", { MethodName : 'verificaResp', data : parameters }, function(rs){
							formPopulated = false;
							jsLIB.populateForm( $("#Resp"), rs);
							formPopulated = true;
						});
						
					} else {
						var parameters = {
							id	: (field.startsWith("cad_resp") ? $("#respID").val() : membroID),
							field	: field,
							val	: value
						}
						//gravar
						jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/membros.php", { MethodName : 'updateMember', data : parameters }, function(mb){
							//tratamento de dependencias
							if (field == "cad_pessoa-tp_sexo"){
								populateUnidade(membroID);
								populateCargos(membroID);
							} else if (field == "cad_pessoa-dt_nasc"){
								$("#nrIdade").val( mb.membro.nr_idade );
								fMostraAba( mb.membro.nr_idade < 18, $("#abaResponsavel"), $("#Resp") );
								populateUnidade(membroID);
								populateCargos(membroID);
							} else if (field == "cad_pessoa-cep"){
								var ect = jsLIB.consultaCEP( value );
								if (ect.cep){
									$("#dsLogra").val(ect.cep.lg).attr('valid','ok').trigger("change");
									$("#nrLog").val(ect.cep.nr).attr('valid','ok').trigger("change");
									$("#dsBai").val(ect.cep.ba).attr('valid','ok').trigger("change");
									$("#dsCid").val(ect.cep.cd).attr('valid','ok').trigger("change");
									$("#cmUF").val(ect.cep.uf).attr('valid','ok').trigger("change");
								}
							} else if (field == "cad_ativos-id_unidade"){
								populateCargos(membroID);
							} else if (field == "cad_ativos-cd_cargo"){
								fMostraDiretoria();
							} else if (field == "fg_ativo"){
								if (mb.result == true){
									populateMember(mb);
								}
							}
						});
					}
				}
			}
		})
	;
		
	$('.panel-heading').on("click", function (e) {
		if ( !$(this).hasClass('panel-collapsed') ) {
			$(this).addClass('panel-collapsed');
			$(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
		} else {
			$(this).removeClass('panel-collapsed');
			$(this).find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
		}
	});

	$('#membrosModal .modal-footer').click(function(e){
		e.preventDefault();
		e.stopPropagation();
	});
	$('#membrosModal').on('hidden.bs.modal', function(e){
		closeCollapseAll();
		dataTable.ajax.reload();
	});

	$("#nrUltEstrela, #nrUniformes").TouchSpin({
		verticalbuttons: true,
		verticalupclass: 'glyphicon glyphicon-plus',
		verticaldownclass: 'glyphicon glyphicon-minus'
	});

	$('#membrosDatatable tbody').on('click', 'tr', function () {
		rowSelected = this;
		populateMember( getMember( dataTable.row( rowSelected ).data().id ) );
		fAbaFirstFocus();
		ruleButtons();
		$("#membrosModal").modal();
	});
	
	$('[name=memberNavigate]').click(function(e){
		if ( $(this).isEnabled() ) {
			var row = undefined;
			if ( $(this).attr('type-nav') == "next") {
				row = rowSelected.rowIndex + 1;
			} else if ( $(this).attr('type-nav') == "prior") {
				row = rowSelected.rowIndex - 1;
			} else if ( $(this).attr('type-nav') =="first") {
				row = 1;
			} else if ( $(this).attr('type-nav') == "last") {
				row = dataTable.page.info().end;
			}
			if ( row ) {
				rowSelected = dataTable.table().body().rows[row-1];
				populateMember( getMember( dataTable.row( rowSelected ).data().id ) );
				ruleButtons();
			}
		}
	});

	$('.date').mask('00/00/0000');
	$('.cpf').mask('000.000.000-00');
	$('.cep').mask('00000-000');
	$('.sp_celphones').mask(SPMaskBehavior, spOptions);
	ruleButtonSelection( tpFiltro );
});

function ruleButtons() {
	$("#navFirst, #navPrior").enable(true);
	$("#navNext, #navLast").enable(true);
	if ( dataTable.page.info().recordsDisplay < 2 ) {
		$("#navFirst, #navPrior").enable(false);
		$("#navNext, #navLast").enable(false);
	} else if ( rowSelected.rowIndex == 1 ) {
		$("#navFirst, #navPrior").enable(false);
	} else if ( rowSelected.rowIndex == dataTable.page.info().end ) {
		$("#navNext, #navLast").enable(false);
	}
}

function populateUnidade(membroID) {
	formPopulated = false;
	var value = $("#cmUnidade").val();
	jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/membros.php", { MethodName : 'getUnidades', data : { id : membroID } },function(un){
		jsLIB.populateOptions( $("#cmUnidade"), un );
		$("#cmUnidade").val(value).change();
		formPopulated = true;
	});
}

function populateCargos(membroID) {
	formPopulated = false;
	var value = $("#cmCargo").val();
	jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/membros.php", { MethodName : 'getCargos', data : { id : membroID } }, function(){
		jsLIB.populateOptions( $("#cmCargo"), cg );
		$("#cmCargo").val(value).change();
	
		if ( value.startsWith("2-07") ) {
			jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/membros.php", { MethodName : 'getCargos', data : { id : membroID, tp : true } }, function(cg){
				jsLIB.populateOptions( $("#cmCargo2"), cg );
			});
		}
		fMostraDiretoria();
		formPopulated = true;
	});

}

function getMember( membroID ) {
	jsLIB.modalWaiting(true);
	var parameters = { 
		filtro: tpFiltro,
		id : membroID
	};
	return jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/membros.php", { MethodName : 'getMember', data : parameters } );
	jsLIB.modalWaiting(false);
}

function populateMember( mb ) {
	formPopulated = false;
	
	jsLIB.populateOptions( $("#cmAnoDir"), mb.anos );
	jsLIB.populateOptions( $("#cmFanfarra"), mb.instrumentos );
	jsLIB.populateOptions( $("#cmUnidade"), mb.unidades );
	jsLIB.populateOptions( $("#cmCargo"), mb.cargos );
	if ( mb.cargos2 ) {
		jsLIB.populateOptions( $("#cmCargo2"), mb.cargos2 );
	}
	jsLIB.populateForm( $("#cadMembrosForm"), mb.membro );
	$("#nrIdade").val( mb.membro.nr_idade );

	fMostraDiretoria();
	fMostraAba( mb.membro.fg_ativo == 'S', $("#abaAtribuicoes") )
	fMostraAba( mb.membro.nr_idade < 18, $("#abaResponsavel"), $("#Resp") );
	formPopulated = true;
}

function ruleButtonSelection( filtro ){
	if ( filtro == 'A' && !$('#btnAtivos').hasClass("btn-primary") ) {
		$('#btnAtivos').removeClass("btn-primary-outline").addClass("btn-primary");
		$('#btnTodos').removeClass("btn-primary").addClass("btn-primary-outline");
	} else if ( filtro == 'T' && !$('#btnTodos').hasClass("btn-primary") ) {
		$('#btnTodos').removeClass("btn-primary-outline").addClass("btn-primary");
		$('#btnAtivos').removeClass("btn-primary").addClass("btn-primary-outline");
	}
}

function switchSelecion( filtro ) {
	tpFiltro = filtro;
	ruleButtonSelection( filtro );
	dataTable.ajax.reload( function(){
		ruleBtnNovo();
	});
}

function ruleBtnNovo(){
	$("#btnNovo").visible( tpFiltro == 'T' && dataTable.page.info().recordsDisplay == 0 );
}

function closeCollapseAll() {
	$('#membrosModal .collapse').collapse('hide');
	$('#membrosModal .panel-heading').each( function() {
		$(this).removeClass('panel-collapsed');
		$(this).find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
	});
}

function fAbaFirstFocus(){
	$('.nav-pills a[href="#Pessoal"]').tab('show');
}

function fMostraDiretoria(){
	var cargo = $("#cmCargo").val();
	$("#divDiretoria").visible(cargo && cargo.startsWith("2-"));
	$("#divCargo2").visible(cargo && cargo.startsWith("2-07"));
}

function fMostraAba(lMostra,objAba,objFields){
	if (lMostra){
		objAba.show();
	} else {
		if ( $(".nav-pills .active").length > 0 && $(".nav-pills .active").get(0).innerText == objAba.get(0).innerText ) {
			fAbaFirstFocus();
		}
		objAba.hide();
		if ( objFields ) {
			objFields.find("input[type=text], select").val("").change();
		}
	}
}