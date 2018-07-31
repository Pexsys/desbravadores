let comDataTable = undefined; 
let rowSelected = undefined;
let formPopulated = false;
let custos = undefined;
 
$(document).ready(function(){ 
 
	$('.date').mask('00/00/0000');
	$('.cpf').mask('000.000.000-00');
	$('.sp_celphones').mask(SPMaskBehavior, spOptions);
	
	comDataTable = $('#comDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ acordos",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type  : "GET",
			url  : jsLIB.rootDir+"rules/acordos.php",
			data  : function (d) {
				d.MethodName = "acordos",
					d.data = {
						filtro: 'T',
						filters: jsFilter.jSON()
					}
			},
			dataSrc: "source"
		},
		columns: [ 
			{  data: "id", 
				visible: false 
			},
			{  data: 'cd',
				sortable: true,
				width: "10%"
			},
			{  data: 'pt',
				type: 'ptbr-string',
				sortable: true,
				width: "40%"
			},
			{  data: 'bn',
				type: 'ptbr-string',
				sortable: true,
				width: "40%"
			},
			{  data: "tp",
				type: 'ptbr-string',
				width: "10%",
				render: function (data, type, row) {
					if (data == 'P'){
						return "PENDENTE";
					} else if (data == 'L'){
						return "LIBERADO";
					} else {
						return "CONCLUÍDO";
					} 
				} 
			} 
		], 
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) { 
			if (aData.tp == "P") {
				$(nRow.cells[3]).css('color', '#ff6464' ).css('font-weight', 'bold');
			} else if ( aData.tp == "L" ) {
				$(nRow.cells[3]).css('color', '#b0ffb3' );
			} else {
				$(nRow.cells[3]).css('color', '#b0ffb3' );
			}
		}
	}).order( [ 4, 'asc' ] );

	$("#patrForm")
		.on('init.field.fv', function(e, data) {
			if (data.element.attr('type') == 'checkbox' ) {
				data.element.attr('valid','ok');
			} else {
				data.element.attr('valid','not-ok');
			}
		})
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})
		.on('err.field.fv', function(e, data) {
			data.element.attr('valid','not-ok');
			$($(this).parents(".panel").get(0)).removeClass("panel-success").addClass("panel-danger");
			$("#divMembros").visible(false);
			$("[name=accAcordoFinanceiro]").visible(false);
		})
		.on('success.field.fv', function(e, data) {
			data.element.attr('valid','ok');
			let valid = (data.fv.getInvalidFields().length == 0);
			if (valid) {
				$($(this).parents(".panel").get(0)).removeClass("panel-danger").addClass("panel-success");
			}
			$("#divMembros").visible(valid);
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
				nrCPFPatr: {validators: {
					id: {
						country: 'BR',
						message: 'CPF inv&aacute;lido'
					}
				}},
				nmCompletoPatr: {validators: {
					notEmpty: {
						message: 'O nome completo do patrocinador &eacute; obrigat&oacute;rio'
					},
					regexp: {
						regexp: /^([a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\']{2,})+(?:\s[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\']{1,})+$/,
						message: 'Digite no m&iacute;nimo o nome e sobrenome sem espa&ccedil;os no final'
					}
				}},
				dtNascPatr: {validators: {
					excluded: false,
					notEmpty: {
						message: 'A data de nascimento n&atilde;o pode ser vazia'
					},
					date: {
						format: 'DD/MM/YYYY',
						message: 'Data de nascimento inv&aacute;lida!'
					}
				}},
				dsEmailPatr: {validators: {
					regexp: {
						regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
						message: 'Formato de email inv&aacute;lido'
					}
				}},
				nrFonePatr: {validators: {
					notEmpty: {
						message: 'Telefone obrigat&oacute;rio'
					}
				}}
			}
		})
		.on("change", "[field]", function(e) {
			$("#patrForm").formValidation('revalidateField', $(this));

			if (formPopulated) {
				var input = $(this);
				var field = input.attr('field');
				var value = jsLIB.getValueFromField(input);

				if (field && input.attr('valid') == 'ok'){
					if (field == "cad_pessoa-nr_cpf"){
						personByCPF(value, data => populatePersonScope($("#patrForm"),data.source[0]));
					}
				}
			}
		})
	;

	const recuperaFinanceiro = (data) => {
		$("[name=accAcordoFinanceiro]").each( (i,element) => {
			let panel = $(element);
			let grupo = panel.attr('panel-grp');
			let id = panel.attr('panel-id');
			let body = panel.find(".panel-body tbody");

			jsLIB.ajaxCall({
				type: "GET",
				url: jsLIB.rootDir+"rules/acordos.php",
				data: { MethodName : 'financeiroPessoa', data : { panel: id, pessoa: data.id } },
				success: function(res){
					let membro = body.find("[name=name]:contains('"+data.nm+"')");
					if (membro.length){
						console.log('achei, atualizar valor',{membro, res, data, panel, grupo, id});
					} else {
						console.log('nao achei, criar',{membro, res, data, panel, grupo, id});
						let template = `
						<tr>
							<td name=\"name\">${data.nm}</td>
							<td name=\"vl\">1.000,00</td>
							<td><input type=\"checkbox\" ${(grupo == 'DB' || grupo == 'IN' || grupo == 'AN' ? "checked disabled" : "")}/></td>
						</tr>
						`;
						body.append( $(template) );
					}
				}
			});
		});
	}
	const cpfValidators = {
		validators: {
			id: {
				country: 'BR',
				message: 'CPF inv&aacute;lido'
			}
		}
	},
	nameValidators = {
		validators: {
			notEmpty: {
				message: 'O nome completo &eacute; obrigat&oacute;rio'
			},
			regexp: {
				regexp: /^([a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\']{2,})+(?:\s[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\']{1,})+$/,
				message: 'Digite no m&iacute;nimo o nome e sobrenome sem espa&ccedil;os no final'
			}
		}
	},
	dateValidators = {
		validators: {
			excluded: false,
			notEmpty: {
				message: 'A data de nascimento n&atilde;o pode ser vazia'
			},
			date: {
				format: 'DD/MM/YYYY',
				message: 'Data de nascimento inv&aacute;lida!'
			}
		}
	},
	typeahead = {
		hint: true,
		minLength: 3,
		source: function(query,callback) {
			inputTyped = $(this.$element);
			jsLIB.ajaxCall({
				async: false,
				type: "GET",
				url: jsLIB.rootDir+"rules/acordos.php",
				data: { MethodName : 'beneficiados', data : { query } },
				success: function(data){
					callback(data.source);
				}
			});
		},
		displayText: item => item.nm,
		afterSelect: item => {
			const row = inputTyped.parents(".row:first");
			return populatePersonScope(row,item,recuperaFinanceiro);
		},
		autoSelect: true
	};
 	let inputTyped = undefined,
	mbIndex = 0;
	$("#mbForm")
		.on('init.field.fv', function(e, data) {
			if (data.element.attr('type') == 'checkbox' ) {
				data.element.attr('valid','ok');
			} else {
				data.element.attr('valid','not-ok');
			}
		})
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})
		.on('err.field.fv', function(e, data) {
			data.element.attr('valid','not-ok');
			$($(this).parents(".panel").get(0)).removeClass("panel-success").addClass("panel-danger");
			$("[name=accAcordoFinanceiro]").visible(false);
		})
		.on('success.field.fv', function(e, data) {
			data.element.attr('valid','ok');
			let valid = (data.fv.getInvalidFields().length == 0);
			if (valid) {
				$($(this).parents(".panel").get(0)).removeClass("panel-danger").addClass("panel-success");
			}
			$("[name=accAcordoFinanceiro]").visible(valid);
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
                'mb[0].cpf': cpfValidators,
                'mb[0].name': nameValidators,
                'mb[0].date': dateValidators
            }
		})
		.on('click', '.addButton', function() {
            mbIndex++;
            let $template = $('#benefitTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .attr('data-mb-index', mbIndex)
                                .insertBefore($template);
			$clone
				.find('[field="cad_pessoa-id_cad_pessoa"]').attr('name', `mb[${mbIndex}].id`).attr('id', `mb${mbIndex}id`).end()
                .find('[field="cad_pessoa-nr_cpf"]').attr('name', `mb[${mbIndex}].cpf`).attr('id', `mb${mbIndex}cpf`).end()
                .find('[field="cad_pessoa-nm"]').attr('name', `mb[${mbIndex}].name`).attr('id', `mb${mbIndex}name`).typeahead(typeahead).end()
                .find('[field="cad_pessoa-dt_nasc"]').attr('name', `mb[${mbIndex}].date`).attr('id', `mb${mbIndex}date`).end();
            $('#mbForm')
                .formValidation('addField', `mb[${mbIndex}].cpf`, cpfValidators)
                .formValidation('addField', `mb[${mbIndex}].name`, nameValidators)
				.formValidation('addField', `mb[${mbIndex}].date`, dateValidators);
			validateformFields($('#mbForm'));
		})
		.on('click', '.removeButton', function() {
            let $row  = $(this).parents(".row:first"),
				index = $row.attr('data-mb-index');
            $('#mbForm')
                .formValidation('removeField', $row.find(`[name="mb[${index}].cpf"]`))
                .formValidation('removeField', $row.find(`[name="mb[${index}].name"]`))
                .formValidation('removeField', $row.find(`[name="mb[${index}].date"]`));
			$row.remove();
			validateformFields($('#mbForm'));
        })
		.on("change", "[field]", function(e) {
			$("#mbForm").formValidation('revalidateField', $(this));

			if (formPopulated) {
				var input = $(this);
				var field = input.attr('field');
				var value = jsLIB.getValueFromField(input);

				if (field && input.attr('valid') == 'ok'){
					if (field == "cad_pessoa-nr_cpf"){
						personByCPF(value, data => populatePersonScope(input.parents(".row:first"),data.source[0],recuperaFinanceiro));
					}
				}
			}
		})
	;
 
    $("#btnNovo").on("click", function(event){
		alternaFormularios(true);
		formPopulated = false;
		$("#accAcordo .panel-collapse:first").collapse('show');
		$("#accAcordo .panel").each( (i,panel) => {
			$(panel)
					.removeClass("panel-success")
					.addClass("panel-danger");
			const form = $(panel).find("form");
			if (form.length) {
				form.data('formValidation').resetForm(true);
				jsLIB.resetForm(form);
			}
		});
		$("#divMembros, [name=accAcordoFinanceiro]").hide();
		formPopulated = true;
    });
 
    $("#btnFechar").on("click", function(event){ 
        telaInicial();
    }); 
 
    $("#btnGravar").on("click", function(event){
        telaInicial();
	});

	$('.panel').on('shown.bs.collapse', function (e) {
		formPopulated = false;
		e.preventDefault();
		e.stopImmediatePropagation();
		validateformFields( $(this).find("form:first") );

		let panel = $(this);
		if (panel.attr('name') == 'accAcordoFinanceiro'){
			let grupo = panel.attr('panel-grp');
			let id = panel.attr('panel-id');
		}
		formPopulated = true;
	});

	$("#nmCompletoPatr").typeahead({
		hint: true,
		minLength: 3,
		source: (query,callback) => jsLIB.ajaxCall({
			async: false,
			type: "GET",
			url: jsLIB.rootDir+"rules/acordos.php",
			data: { MethodName : 'patrocinadores', data : { query } },
			success: function(data){
				callback(data.source);
			}
		}),
		displayText: item => item.nm,
		afterSelect: item => populatePersonScope( $("#patrForm"), item),
		autoSelect: true
	});
	
	$("[comum='lista']").typeahead(typeahead);
});

function personByCPF(cpf,callback){
	jsLIB.ajaxCall({
		async: false,
		type: "GET",
		url: jsLIB.rootDir+"rules/acordos.php",
		data: { MethodName : 'personByCPF', data : { cpf } },
		success: function(data){
			callback(data);
		}
	})
}

function populatePersonScope(scope,f,callback){
	if (!(scope&&f)) return;
	formPopulated = false;
	jsLIB.populateForm(scope,{
		"cad_pessoa-id_cad_pessoa": f.id || '',
		"cad_pessoa-nr_cpf": f.cp || '',
		"cad_pessoa-nm": f.nm || '',
		"cad_pessoa-dt_nasc": f.dt || '',
		"cad_pessoa-tp_sexo": f.sx || '',
		"cad_pessoa-email": f.em || '',
		"cad_pessoa-fone_cel": f.fn || ''
	},callback ? callback(f) : undefined);
	formPopulated = true;
}

function validateformFields(form){
	if (form.length){
		let fields = form.find(":input");
		if (fields.length > 1) {
			form.formValidation('revalidateField', fields[1].id);
			form.data('formValidation').validate();
		}
	}
}

function telaInicial(){
	refreshDataTable();
	alternaFormularios(false);
}
 
function alternaFormularios(exibe){ 
    $("#divLista").visible(!exibe); 
    $("#divAcordo").visible(exibe); 
} 
 
function refreshDataTable(){
    comDataTable.ajax.reload( function(){
	}); 
}