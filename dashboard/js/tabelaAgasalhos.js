var comDataTable = undefined;

$(document).ready(function(){

	comDataTable = $('#comDataTable').DataTable({
		lengthChange: false,
		ordering: false,
		paging: false,
		scrollY: 300,
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
					d.MethodName = "getAgasalhos"
				},
			dataSrc: "source"
		},
		columns: [
			{	data: 'cd',
				sortable: true
			}
		]
	});

});