var dataTable = undefined;

$(document).ready(function(){
	dataTable = $('#tabPerfisTable')
		.DataTable({
			lengthChange: false,
			ordering: true,
			paging: false,
			scrollY: 300,
			searching: false,
			processing: true,
			language: {
				info: "_END_ perfis",
				search: "",
				searchPlaceholder: "Procurar...",
				infoFiltered: " de _MAX_",
				loadingRecords: "Aguarde - carregando...",
				zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o. Verifique opções de filtro.",
				infoEmpty: "0 encontrados"
			},
			ajax: {
				type	: "POST",
				url	: jsLIB.rootDir+"rules/tabelaPerfis.php",
				data	: function (d) {
					d.MethodName = "getPerfis"
				},
				dataSrc: "source"
			},
			order: [ 1, 'asc' ],
			columns: [
				{	data: "id"
				},
				{	data: "ds",
					type: 'ptbr-string',
					sortable: true,
					width: "50%"
				}
			],
			columnDefs: [
				{
					targets: [ 0 ],
					visible: false,
					searchable: false
				}
			]
		})
	;
	
	$("#cadListaForm")
		.on('init.field.fv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
			$icon.on('click.clearing', function() {
				if ( $icon.hasClass('glyphicon-remove') ) {
					data.fv.resetField(data.element);
				}
			});
		})
		.on('success.form.fv', function(e) {
			e.preventDefault();
		})	
		.formValidation({
			framework: 'bootstrap',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			}
		})
		.submit( function(event) {
			var parameter = {
				frm: jsLIB.getJSONFields( $('#cadListaForm') )
			};
			jsLIB.ajaxCall({
				waiting : true,
				async: false,
				url: jsLIB.rootDir+"rules/tabelaPerfis.php",
				data: { MethodName : 'addPerfil', data : parameter },
				callBackSucess: function(data){
					dataTable.ajax.reload( function(){
						$("#listaModal").modal('hide');
					});
				}
			});
		})
	;
});