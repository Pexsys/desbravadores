var list = [];
var dataTable = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );
	
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
				jsLIB.rootDir+'report/geraAutorizacao.php?list='+list+'&pid='+$("#nmMembro").val(),
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
			scrollY: 90,
			searching: true,
			processing: true,
			language: {
				info: "_END_ sa&iacute;das",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "POST",
				url	: jsLIB.rootDir+"rules/saidas.php",
				data	: function (d) {
						d.MethodName = "getSaidas",
						d.data = { 
							filter: 'P'
						}
					},
				dataSrc: "saidas"
			},
			order: [ 1, 'asc' ],
			columns: [
				{	data: 'id',
					sortable: false,
					width: "150px"
				},
				{	data: 'ds',
					type: 'ptbr-string',
					sortable: true,
					width: "55%"
				},
				{	data: 'dh_s',
					sortable: true,
					width: "20%",
					render: function (data) {
						return moment.unix(data).format("DD/MM/YYYY HH:mm")
					}
				},
				{	data: 'dh_r',
					sortable: true,
					width: "20%",
					render: function (data) {
						return moment.unix(data).format("DD/MM/YYYY HH:mm")
					}
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

	var saidas = jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/saidas.php", { MethodName : 'getNames' }, 'RETURN' );
	if (saidas.names){
		jsLIB.populateOptions( $("#nmMembro"), saidas.names );
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