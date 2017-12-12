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
	
	$("[name=reprint]").on('click',function(){
	    list = [];
	    list.push( $(this).attr('aut-id') );
        window.open(
			jsLIB.rootDir+'report/geraAutorizacao.php?list='+list+'&pid='+$(this).attr('cad-id'),
			'_blank',
			'top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',
			true
		);
		list = [];
	});

	$('#clearSelection').on('click',function(){
		list = [];
	});
	
	jsLIB.ajaxCall({
		waiting : true,
		type: "GET",
		url: jsLIB.rootDir+"rules/saidas.php",
		data: { MethodName : 'getNames' },
		callBackSucess: function(saidas){
			if (saidas.names && saidas.names.length){
				jsLIB.populateOptions( $("#nmMembro"), saidas.names );
				$("#outrasAutoriz").show();
				$("#alertAutoriz").hide();
			} else if (!$("div#minhasAutoriz").length) {
				$("#outrasAutoriz").hide();
				$("#alertAutoriz").show();
			}
		}
	});
});