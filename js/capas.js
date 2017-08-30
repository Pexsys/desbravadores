var list = [];
var dataTable = undefined;

$(document).ready(function(){
	
	//FORM
	$("#capas-form")
		.on('init.field.fv', function(e, data) {
			// data.fv      --> The FormValidation instance
			// data.field   --> The field name
			// data.element --> The field element

			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');

			// You can retrieve the icon element by
			// $icon = data.element.data('fv.icon');
			$icon.on('click.clearing', function() {
				if ( $icon.hasClass('glyphicon-remove') ) {
					data.fv.resetField(data.element);
				}
			});
		})
		.on('success.validator.fv', function(e, data) {
		})
		.formValidation({
			framework: 'bootstrap',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				cdMembro: {
					validators: {
						stringLength: {
							min: 7,
							max: 7,
							message: 'O c&oacute;digo do usu&aacute;rio deve conter 7 caracteres'
						},
						regexp: {
							regexp: /^[a-zA-Z0-9]+$/,
							message: 'O c&oacute;digo do usu&aacute;rio s&oacute; pode conter letras e n&uacute;meros'
						}
					}
				},
				nmMembro: {
					validators: {
						notEmpty: {
							message: 'O nome completo &eacute; obrigat&oacute;rio'
						},
						different: {
							field: 'cdMembro',
							message: 'O nome deve ser diferente do c&oacute;digo'
						},
						regexp: {
							regexp: /^((\b[a-zA-Z\u00C0-\u00FF]{1,40}\b)\s*){2,}$/,
							message: 'Digite no m&iacute;nimo o nome e sobrenome'
						}
					}
				}
			}
		})
		.on('success.form.fv', function(e) {
			// Prevent form submission
			e.preventDefault();
			updateFields();
		})	
		.submit( function() {
			list.sort();
			window.open(
				jsLIB.rootDir+'report/geraCapa.php?nome='+$("#id").val()+'|'+$("#nmMembro").val()+'&list='+list,
				'_blank',
				'top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',
				true
			);
		});
		
	$("#cdMembro").blur(function() {
		updateFields();
		if ( $(this).val() == "" ) {
			resetNome();
			return;
		}

		var parameter = {
			codigo: $(this).val()
		};
		jsLIB.ajaxCall( false, jsLIB.rootDir+'rules/capas.php', { MethodName : 'getName', data : parameter }, 
			function( data, jqxhr ){
				if ( data.ok == true ) {
					$("#nmMembro").val(data.nome);
					$("#id").val(data.id);
				} else {
					resetNome();
				}
				updateFields();
			});
	});
	
	$("#nmMembro").change(function() {
		$("#id").val("");
	});
	
	dataTable = $('#simpledatatable')
		.DataTable({
			lengthChange: false,
			ordering: true,
			paging: false,
			scrollY: 300,
			searching: true,
			processing: true,
			language: {
				info: "_END_ especialidades",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "POST",
				url		: jsLIB.rootDir+"rules/capas.php",
				data	: function (d) {
							d.MethodName = "getEspecialidades"
						},
				dataSrc: "especialidades"
			},
			order: [ 1, 'asc' ],
			columns: [
				{	data: 'cd_item',
					sortable: false,
					width: "150px"
				},
				{	data: 'ds_item',
					sortable: true,
					width: "50%"
				},
				{	data: 'ds_area',
					sortable: true,
					width: "50%"
				}
			]
		})
	;
		
	$('#simpledatatable tbody').on('click', 'tr', function () {
		lineChecked( $(this) );
	});

	$('#clearSelection').on('click',function(){
		dataTable.$('tr.selected').removeClass('selected');
		list = [];
	});
});

function resetNome(){
	$("#nmMembro").val("");
	$("#id").val("");
	updateFields();
}

function lineChecked( row ) {
	var value = $(row.find("td").get(0)).text();
	var index = $.inArray(value, list);

	if ( index === -1 ) {
		list.push( value );
		row.addClass('selected');
	} else {
		row.removeClass('selected');
		list.splice( index, 1 );
	}	
}

function updateFields(){
	$("#cdMembro").val($("#cdMembro").val().toUpperCase());
	$("#nmMembro").val($("#nmMembro").val().toUpperCase());
	
	$("#capas-form")
		.formValidation('revalidateField', "cdMembro")
		.formValidation('revalidateField', "nmMembro");
}