var comDataTable = undefined;
var rowSelected = undefined;

$(document).ready(function(){

	comDataTable = $('#comDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: false,
		processing: true,
		language: {
			info: "_END_ cargos",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"rules/tabelas.php",
			data	: function (d) {
				d.MethodName = "getCargos"
			},
			dataSrc: "source"
		},
		columns: [
			{	data: 'cd',
				sortable: true,
				width: "10%"
			},
			{	data: 'dm',
				type: 'ptbr-string',
				sortable: true,
				width: "45%"
			},
			{	data: 'df',
				type: 'ptbr-string',
				sortable: true,
				width: "45%"
			}
		]
	}).order( [ 0, 'asc' ] );

});