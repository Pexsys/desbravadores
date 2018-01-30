var comDataTable = undefined;
var rowSelected = undefined;

$(document).ready(function(){

	comDataTable = $('#comDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 150,
		searching: false,
		processing: true,
		language: {
			info: "_END_ tamanhos",
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
					d.MethodName = "getCamisetas"
				},
			dataSrc: "source"
		},
		order: [ 0 ],
		columns: [
			{	data: 'cd',
				sortable: true
			}
		]
	});

});