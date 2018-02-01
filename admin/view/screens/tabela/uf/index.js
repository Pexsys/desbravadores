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
			info: "_END_ UFs",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"admin/rules/tabelas.php",
			data	: function (d) {
					d.MethodName = "getUFs"
				},
			dataSrc: "source"
		},
		order: [ 0 ],
		columns: [
			{	data: 'id',
				sortable: true
			}
		]
	});

});