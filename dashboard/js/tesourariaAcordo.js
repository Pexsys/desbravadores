var comDataTable = undefined; 
var rowSelected = undefined; 
 
$(document).ready(function(){ 
 
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
				d.MethodName = "getAcordos",
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
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})
		.on('err.field.fv', function(e, data) {
			$($(this).parents(".panel").get(0)).removeClass("panel-success").addClass("panel-danger");
			//$("#divAcordoMembros").visible(false);
		})
		.on('success.field.fv', function(e, data) {
			let valid = (data.fv.getInvalidFields().length == 0);
			if (valid) {
				$($(this).parents(".panel").get(0)).removeClass("panel-danger").addClass("panel-success");
			}
			//$("#divAcordoMembros").visible(valid);
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
			$("#patrForm").formValidation('revalidateField', this.id);
		})
	;

	let cpfValidators = {
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
	mbIndex = 0;
	$("#mbForm")
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})
		.on('err.field.fv', function(e, data) {
			$($(this).parents(".panel").get(0)).removeClass("panel-success").addClass("panel-danger");
			//$("#divAcordoFinanceiro").visible(false);
		})
		.on('success.field.fv', function(e, data) {
			let valid = (data.fv.getInvalidFields().length == 0);
			if (valid) {
				$($(this).parents(".panel").get(0)).removeClass("panel-danger").addClass("panel-success");
			}
			//$("#divAcordoFinanceiro").visible(valid);
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
            var $template = $('#benefitTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .attr('data-mb-index', mbIndex)
                                .insertBefore($template);
            $clone
                .find('[field="cad_pessoa-nr_cpf"]').attr('name', `mb[${mbIndex}].cpf`).attr('id', `mb${mbIndex}cpf`).end()
                .find('[field="cad_pessoa-nm"]').attr('name', `mb[${mbIndex}].name`).attr('id', `mb${mbIndex}name`).end()
                .find('[field="cad_pessoa-dt_nasc"]').attr('name', `mb[${mbIndex}].date`).attr('id', `mb${mbIndex}date`).end();
            $('#mbForm')
                .formValidation('addField', `mb[${mbIndex}].cpf`, cpfValidators)
                .formValidation('addField', `mb[${mbIndex}].name`, nameValidators)
				.formValidation('addField', `mb[${mbIndex}].date`, dateValidators);
				
			validateformFields($('#mbForm'));
		})
		.on('click', '.removeButton', function() {
            var $row  = $(this).parents(".row").first(),
				index = $row.attr('data-mb-index');
            $('#mbForm')
                .formValidation('removeField', $row.find(`[name="mb[${mbIndex}].cpf"]`))
                .formValidation('removeField', $row.find(`[name="mb[${mbIndex}].name"]`))
                .formValidation('removeField', $row.find(`[name="mb[${mbIndex}].date"]`));
            $row.remove();
        })
		.on("change", "[field]", function(e) {
			$("#patrForm").formValidation('revalidateField', this.id);
		})
	;
 
    $("#btnNovo").on("click", function(event){ 
        alternaFormularios(true); 
    }); 
 
    $("#btnFechar").on("click", function(event){ 
        telaInicial();
    }); 
 
    $("#btnGravar").on("click", function(event){
        telaInicial();
	});

	$('.date').mask('00/00/0000');
	$('.cpf').mask('000.000.000-00');
	$('.sp_celphones').mask(SPMaskBehavior, spOptions);

	$('.panel').on('shown.bs.collapse', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		validateformFields( $(this).find("form") );
	});
});

var $input = $(".typeahead");
$input.typeahead({
	hint: true,
	highlight: true,
	minLength: 1,
	source: [
		{id: "4", name: "RICARDO JONADABS CÉSAR"},
		{id: "5", name: "HULDA ANDRADE JONADABS CÉSAR"}
	],
	autoSelect: true
})
.on('typeahead:selected', function(e, suggestion, dataSetName) {
	/* Revalidate the state field */
	$('#typeheadForm').formValidation('revalidateField', this.id);
})
.on('typeahead:closed', function(e) {
	/* Revalidate the state field */
	$('#typeheadForm').formValidation('revalidateField', this.id);
});
$input.change(function() {
  var current = $input.typeahead("getActive");
  if (current) {
    // Some item from your model is active!
    if (current.name == $input.val()) {
      // This means the exact match is found. Use toLowerCase() if you want case insensitive match.
    } else {
      // This means it is only a partial match, you can either add a new item
      // or take the active if you don't want new items
    }
  } else {
    // Nothing is active so it is a new value (or maybe empty value)
  }
});

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