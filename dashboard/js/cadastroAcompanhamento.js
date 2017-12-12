var dataTable = undefined;
var dataTable = undefined;
var dataTable = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY' );
	
	dataTable = $('#acompDatatable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ itens",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "POST",
			url	: jsLIB.rootDir+"rules/acompanhamento.php",
			data	: function (d) {
					d.MethodName = "getData",
					d.data = { 
							 filtro: 'T',
							 filters: jsFilter.jSON()
						}
				},
			dataSrc: "acomp"
		},
		columns: [
			{	data: "ip",
				visible: false
			},
			{	data: "ia",
				visible: false
			},
			{	data: "nm",
				type: 'ptbr-string',
				width: "45%"
			},
			{	data: "tp",
				width: "26%"
			},
			{	data: "di",
				width: "8%"
			},
			{	data: "dc",
				width: "8%"
			},
			{	data: "da",
				width: "8%"
			},
			{	data: "pg",
				width: "5%"
			}
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			var row = nRow.cells[5];
			if ( aData.pg < 51 ) {
				$(row).css('color', '#cc0000').css('font-weight', 'bold');
			} else if ( aData.pg < 85 ) {
				$(row).css('color', '#ccaa00');
			} else if ( aData.pg == 100 ) {
				$('td', nRow).css('background-color', '#b3ffb3');
			}
        },
		select: {
			style: 'multi',
			selector: 'td:first-child'
		}
	});
	
	$('#acompDatatable tbody').on('click', 'tr', function () {
		$(this).toggleClass('selected');
	});
	
	$("#cadAcompForm")
		.on('init.field.fv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
			$icon.on('click.clearing', function() {
				if ( $icon.hasClass('glyphicon-remove') ) {
					data.fv.resetField(data.element);
				}
			});
		})
		.on('success.validator.fv', function(e, data) {
			e.preventDefault();
		})
		.on('success.field.fv', function(e, data) {
			e.preventDefault();
			
			var parameter = {
				brdt : $("#cdBar").val(),
				frm: jsLIB.getJSONFields( $('#cadAcompForm') )
			};
			sendBarCode( parameter,
				function(data,fx){
					$("#divResultado").html(data.result);
					var checkbox = $("#divResultado").find("input[type=checkbox]");
					if (checkbox.length > 0){
						checkbox.change(function(){
							var atf = $(this).attr("for");
							if ( $(this).prop('checked') ) {
								$('[field='+atf+']').val( new Date().toFormattedDate() ).visible(true);
							} else {
								$('[field='+atf+']').val('').visible(false);
							}
						});
						checkbox.bootstrapToggle();
						$("#divResultado").find('[name=dt_assinatura]').datetimepicker({
							locale: 'pt-br',
							language: 'pt-BR',
							format: 'DD/MM/YYYY',
							maskInput: true,
							pickDate: true,
							pickTime: false,
							pickSeconds: false,
							useCurrent: false
						});
						$('#btnGravar').visible(true);
					}
					$("#divResultado").show();
				},
				function(data,fx){
					$("#divResultado").empty().hide();
				}
			);
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
				cdBar: {
					validators: {
						stringLength: {
							min: 7,
							max: 7,
							message: 'O c&oacute;digo deve conter 7 caracteres'
						},
						notEmpty: {
                            message: 'Informe o pr&oacute;ximo c&oacute;digo'
                        }
					}
				}
			}
		})
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})
	;
	
	$('#cadAcompForm')
		.submit( function(e) {
			e.preventDefault();
			e.stopPropagation();
		})
	;

	$('#btnNovo').click(function(){
		jsLIB.resetForm( $('#cadAcompForm') );
		$("#barOp").val('ACOMPANHAMENTO');
		$('#btnGravar').visible(false);
		$("#divResultado").visible(false);
		$("#acompModal").modal();
		$("#cdBar").focus();
	});
	
	$('#btnGravar').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		update();
	});

	$(".date").mask('00/00/0000');
});

function update(){
	var parameter = {
		frm: jsLIB.getJSONFields( $('#cadAcompForm') )
	};
	jsLIB.ajaxCall({
		waiting : false,
		async: true,
		url: jsLIB.rootDir+"rules/acompanhamento.php",
		data: { MethodName : 'setRequisito', data : parameter },
		callBackSucess: function(){
			$("#divResultado").empty().hide();
			$("#cdBar").val('').change().focus();
			$('#btnGravar').visible(false);
			dataTable.ajax.reload();
		}
	});
}