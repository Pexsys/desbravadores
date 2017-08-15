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
			$icon = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');

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
		.on('success.field.fv', function(e, data) {
            if (data.fv.getSubmitButton()) {
                data.fv.disableSubmitButtons(false);
            }
        })
		.formValidation({
			framework: 'bootstrap',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				nmMembro: {
					validators: {
						notEmpty: {
							message: 'Selecione um ou mais nomes...'
						}
					}
				}
			}
		})
		.on('success.form.fv', function(e) {
			// Prevent form submission
			e.preventDefault();
		})	
		.submit( function() {
			list.sort();
			window.open(
				jsLIB.rootDir+'report/geraCapa.php?nome='+$("#nmMembro").val()+'&list='+list,
				'_blank',
				'top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',
				true
			);
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
					type: 'ptbr-string',
					sortable: true,
					width: "50%"
				},
				{	data: 'ds_area',
					type: 'ptbr-string',
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
		$('#nmMembro').selectpicker('deselectAll');
	});

	var capas = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/capas.php", { MethodName : 'getNames' }, 'RETURN' );
	if (capas.names){
		jsLIB.populateOptions( $("#nmMembro"), capas.names );
	}
});

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