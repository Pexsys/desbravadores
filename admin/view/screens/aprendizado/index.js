var dataTable = undefined;

$(document).ready(function(){

	dataTable = $('#aprDatatable').DataTable({
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
			type	: "GET",
			url	: jsLIB.rootDir+"admin/rules/aprendizado.php",
			data	: function (d) {
					d.MethodName = "getAprendizado",
					d.data = {
							 filtro: 'T',
							 filters: jsFilter.jSON()
						}
				},
			dataSrc: "aprendizado"
		},
		order: [ 2, 'asc' ],
		columns: [
			{	data: "id",
				visible: false
			},
			{	data: "ip",
				visible: false
			},
			{	data: "nm",
				type: 'ptbr-string',
				width: "50%"
			},
			{	data: "dsitp",
				width: "15%"
			},
			{	data: "dsitm",
				width: "35%"
			}
		],
		columnDefs: [
			{
				targets: [ 0 ],
				visible: false,
				searchable: false
			}
		],
		select: {
			style: 'multi',
			selector: 'td:first-child'
		}
	});

	$("#cadAprendForm")
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
			updateAprendizado();
		})
	;

	$("#cadAprendBarForm")
		.on('init.field.fv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
			$icon.on('click.clearing', function() {
				if ( $icon.hasClass('glyphicon-remove') ) {
					data.fv.resetField(data.element);
				}
			});
		})
		.on('err.field.fv', function(e, data) {
			e.preventDefault();
			//$("#divResultado").empty().hide();
		})
		.on('success.validator.fv', function(e, data) {
			e.preventDefault();
		})
		.on('success.field.fv', function(e, data) {
			e.preventDefault();
			if ($("#cdBar").val() != ''){
				onscan( $("#cdBar").val().toUpperCase() );
			}
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
						}
					}
				}
			}
		})
		.on('success.form.fv', function(e) {
			// Prevent form submission
			e.preventDefault();
		})
	;

	$('#aprendBarModal')
		.on('hidden.bs.modal', function(e){
			dataTable.ajax.reload( function(){
				ruleBtnDelete();
			});
		})
	;

	$('[name=toggle-dates]').on("change",function(e) {
		var value = jsLIB.getValueFromField($(this));
		$("#"+$(this).attr("for")).visible( value == 'S' );
	});

	$('[tab-function]').on('click', function(event){
		var objOrigem = $(this);
		if ( objOrigem.attr("tab-function") == "radio" ) {
			$('[tab-function=radio]').each(function(){
				$( '#'+$(this).attr('for') ).visible(false);
			});
		}
		$( '#'+objOrigem.attr('for') ).visible( !objOrigem.hasClass("active") );
	});

	$('#btnManual').click(function(){
		fPrepareForm();
	});

	$('#btnDigital').click(function(){
		jsLIB.resetForm( $('#cadAprendBarForm') );
		$("#barOp").val('APRENDIZADO');
		$("#divResultado").visible(false);
		$("#aprendBarModal").modal();
		$("#cdBar").focus();
	});

	$('#aprDatatable tbody').on('click', 'tr', function () {
		$(this).toggleClass('selected');
		ruleBtnDelete();
	});

	$('#btnDelAprend').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma exclus&atilde;o das linhas selecionadas?',
			type: BootstrapDialog.TYPE_WARNING,
			size: BootstrapDialog.SIZE_SMALL,
			draggable: true,
			closable: true,
			closeByBackdrop: false,
			closeByKeyboard: false,
			buttons: [
				{ label: 'N&atilde;o',
					cssClass: 'btn-success',
					action: function( dialogRef ){
						dialogRef.close();
					}
				},
				{ label: 'Sim, desejo excluir!',
					icon: 'glyphicon glyphicon-trash',
					cssClass: 'btn-danger',
					autospin: true,
					action: function(dialogRef){
						dialogRef.enableButtons(false);
						dialogRef.setClosable(false);

						var selected = dataTable.rows('.selected').data();
						var tmp = [];
						for (var i=0;i<selected.length;i++){
							tmp.push(selected[i].id);
						}
						var parameter = {
							ids: tmp
						};
						jsLIB.ajaxCall({
							waiting : true,
							url: jsLIB.rootDir+"admin/rules/aprendizado.php",
							data: { MethodName : 'delete', data : parameter },
							success: function(){
								dialogRef.close();
								closeAndRefresh();
							}
						});
					}
				}
			]
	    });
	});

	$("#cmClasse").change(function(){
		$("#divIdent").visible( $(this).val() != null );
	});

	//$('#dtInicio, #dtConclusao, #dtAvaliacao, #dtInvestidura, #dtBarInicio, #dtBarConclusao, #dtBarAvaliacao, #dtBarInvestidura').datetimepicker({
	//	locale: 'pt-br',
	//	language: 'pt-BR',
	//	format: 'DD/MM/YYYY',
	//	maskInput: true,
	//	pickDate: true,
	//	pickTime: false,
	//	pickSeconds: false,
	//	useCurrent: false
	//});

	//$(".date").mask('00/00/0000');
	ruleBtnDelete(false);
});

function onscan( bardata ) {
	var parameter = {
		brdt : bardata,
		frm: jsLIB.getJSONFields( $('#cadAprendBarForm') )
	};
	sendBarCode( parameter,
		function(data,fx){
			$("#strResultado").html(data.result);
			$("#divResultado").show();
			$("#cdBar").val('').change().focus();
		},
		function(data,fx){
			$("#strResultado").hide();
			$("#cdBar").val('').change().focus();
		}
	);
}

function ruleBtnDelete( force ){
	$("#btnDelAprend").visible( force != undefined ? force : dataTable.rows('.selected').data().length > 0 );
}

function updateAprendizado(){
	var parameter = {
		frm: jsLIB.getJSONFields( $('#cadAprendForm') )
	};
	getFunctions( parameter, "quem" );
	jsLIB.ajaxCall({
		waiting : true,
		url: jsLIB.rootDir+"rules/aprendizado.php",
		data: { MethodName : 'setAprendizado', data : parameter },
		success: function(){
			closeAndRefresh();
		}
	});
}

function getFunctions(parameter,fn){
	var retorno = {};
	var lista = false;
	$("[name="+fn+"].active").each( function() {
		var id = $(this).attr('for');
		if (id == "Lista" || id == "Selec"){
			lista = true;
			var selected = dataTable.rows( { filter : 'applied'} ).data();
			if (id == "Selec"){
				selected = dataTable.rows('.selected').data();
			}
			var tmp = [];
			for (var i=0;i<selected.length;i++){
				tmp.push(selected[i].id);
			}
			retorno = tmp;
		}
	});
	if (lista){
		parameter["frm"]["id"] = retorno;
	}
}

function fPrepareForm(){
	$("[name=oque]").removeClass('active');
	$("[name=quem]").removeClass('active');

	$('[tab-function]').each(function(event){
		$( '#'+$(this).attr('for') ).visible( false );
	});

	populateData();
	jsLIB.resetForm( $('#cadAprendForm') );
	$("#aprendModal").modal();
}

function closeAndRefresh(){
	dataTable.ajax.reload( function(){
		updateNotifications(); 
		ruleBtnDelete( false ); 
		$("#aprendModal").modal('hide'); 
	});
}

function populateData(){
	jsLIB.ajaxCall({
		waiting : true,
		type: "GET",
		url: jsLIB.rootDir+"admin/rules/aprendizado.php",
		data: { MethodName : 'getData' },
		success: function(cg){
			jsLIB.populateOptions( $("#cmNome"), cg.nomes );
			jsLIB.populateOptions( $("#cmClasse"), cg.classe );
			jsLIB.populateOptions( $("#cmIdent"), cg.tags );
			jsLIB.populateOptions( $("#cmEspec"), cg.especialidade );
			jsLIB.populateOptions( $("#cmMest"), cg.mestrado );
			jsLIB.populateOptions( $("#cmMeri"), cg.merito );
		}
	});
}
