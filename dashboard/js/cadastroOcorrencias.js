var ocoDataTable = undefined;
var rowSelected = undefined;
var valuePend = undefined;
var valuePendOrig = undefined;

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );

	ocoDataTable = $('#ocoDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 150,
		searching: true,
		processing: true,
		language: {
			info: "_END_ ocorr&ecirc;ncias",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "POST",
			url	: jsLIB.rootDir+"rules/ocorrencias.php",
			data	: function (d) {
					d.MethodName = "getOcorrencias",
					d.data = { 
						filtro: 'A',
						filters: jsFilter.jSON()
					}
				},
			dataSrc: "ocorr"
		},
		order: [ 2, 'desc' ],
		columns: [
			{	data: 'id',
				visible: false
			},
			{	data: 'so',
				visible: false
			},
			{	data: 'cd',
				sortable: true,
				width: "10%"
			},
			{	data: 'nm',
				sortable: true,
				width: "50%"
			},
			{	data: 'tp',
				sortable: true,
				width: "10%",
				render: function (data) {
					return (data == 'P' ? "Positiva" : "Negativa");
				}
			},
			{	data: 'dh',
				sortable: true,
				width: "15%",
				render: function (data) {
					return moment.unix(data).format("DD/MM/YYYY")
				}
			},
			{	data: 'st',
				sortable: true,
				width: "15%",
				render: function (data) {
					return (data == 'S' ? "RASCUNHO" : "Enviada");
				}
			}
		],
		fnInitComplete: function(oSettings, json) {
		  buttonsPrimary();
		},		
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        	if ( aData.dh < moment().unix() ) {
           		$('td', nRow).css('color', '#d0d0d0');
        	}
			if ( aData.tp == 'P' ) {
				$(nRow.cells[2]).css('color', '#00cc00');
			} else {
				$(nRow.cells[2]).css('color', '#cc0000');
			}
			if ( aData.st == 'S' ) {
				$(nRow.cells[4]).css('color', '#cc0000').css('font-weight', 'bold');
			}
        }
	});
	
	$('#datetimepicker').datetimepicker({
		locale: 'pt-br',
		language: 'pt-BR',
		format: 'DD/MM/YYYY',
		maskInput: true,
		pickDate: true,
		pickTime: false,
		pickSeconds: false,
		useCurrent: false
	}).on('dp.change',function(){
		$("#cadOcoForm").formValidation('revalidateField', 'ocoDH');
		buttons();
	});

	tinymce.init({
		selector: '[type=wysiwyg]',
		height: 180,
		language: 'pt_BR',
		plugins: [
			"advlist link image lists print hr spellchecker",
		    "searchreplace wordcount code media nonbreaking",
		    "table contextmenu directionality textcolor paste textcolor colorpicker textpattern"
		],
		toolbar1: "newdocument | fontselect fontsizeselect | bold italic underline strikethrough | subscript superscript | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent",
		toolbar2: "undo redo | cut copy paste | searchreplace | image code | forecolor backcolor | table | spellchecker",
		fontsize_formats: '2px 3px 4px 5px 6px 7px 8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px',
		content_style: "p{margin: 0px; padding: 0px}, br{margin: 0px; padding: 0px}",
		toolbar_items_size: 'small',
		save_enablewhendirty: false,
		menubar: false,
		browser_spellcheck: true,
		spellchecker_languages: 'Portugues=pt_BR',
		spellchecker_rpc_url: 'spellchecker.php',
		statusbar: false
	});

	$("#nrCopias").TouchSpin({
		verticalbuttons: true,
		verticalupclass: 'glyphicon glyphicon-plus',
		verticaldownclass: 'glyphicon glyphicon-minus'
	});

	$('#ocoDataTable tbody').on('click', 'tr', function () {
		rowSelected = this;
		valuePendOrig = ocoDataTable.row( rowSelected ).data().so;
		populateOcorrencia( ocoDataTable.row( rowSelected ).data().id );
		$("#ocoModal").modal();
	});
	
	$("#cadOcoForm")
		.on("change", "[field]", function(e) {
			$("#cadMembrosForm")
				.formValidation('revalidateField', this.id);
		})
		.on('err.field.fv', function(e, data) {
			$('#btnGravar').visible(false);
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
				ocoDH: {
					validators: {
						excluded: false,
						notEmpty: {
							message: 'A data da ocorrência n&atilde;o pode estar em branco'
						},
						date: {
							format: 'DD/MM/YYYY',
							message: 'Data inv&aacute;lida!'
						}
					}
				},
				cmNome: {
					validators: {
						notEmpty: {
							message: 'Selecione a pessoa da ocorr&ecirc;ncia'
						}
					}
				}
			}
		})
		.submit( function(e) {
			e.preventDefault();
			e.stopPropagation();
		})
	;
	
	$('#btnDel').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma exclus&atilde;o desta ocorrência?',
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
						var parameter = {
							id: $('#ocoID').val(),
							op: "DELETE"
						};
						jsLIB.ajaxCallOld( false, jsLIB.rootDir+"rules/ocorrencias.php", { MethodName : 'fOcorrencia', data : parameter }, function(dt){
							refreshAndButtons();
							dialogRef.close();
							$("#ocoModal").modal('hide');
						});
					}
				}
			]
		});
	});	
	
	$('#btnGravar').click(function(){
		if (valuePendOrig !== valuePend) {
			BootstrapDialog.show({
				title: 'Alerta',
				message: 'Confirma gravação, envio e geração desta ocorrência?',
				type: BootstrapDialog.TYPE_SUCCESS,
				size: BootstrapDialog.SIZE_SMALL,
				draggable: true,
				closable: true,
				closeByBackdrop: false,
				closeByKeyboard: false,
				buttons: [
					{ label: 'N&atilde;o',
						cssClass: 'btn-warning',
						action: function( dialogRef ){
							$("#fgPend").prop('checked', false).change();
							dialogRef.close();
						}
					},
					{ label: 'Sim, desejo gravar!',
						icon: 'glyphicon glyphicon-trash',
						cssClass: 'btn-danger',
						autospin: true,
						action: function(dialogRef){
							dialogRef.enableButtons(false);
							dialogRef.setClosable(false);
							updateOcorrencia();
							updateNotifications();
							refreshAndButtons();
							dialogRef.close();
						}
					}
					]
			});		
		} else {
			updateOcorrencia();
			refreshAndButtons();
		}
	});
	
	$("#ocoModal").on('show.bs.modal', function(event){
		buttons();
	});
	
	$('#btnNovo').click(function(){
		jsLIB.resetForm( $('#cadOcoForm') );
		populateOcorrencia( $("#ocoID").val("Novo").val() );
		buttons();
		$("#ocoModal").modal();
	});
	
	$("#fgPend").change(function(){
		valuePend = jsLIB.getValueFromField( $("#fgPend") );
		buttons();
	});

	$("#cmNome").change(function(){
		buttons();
	});
	
	$('#btnPrepare').click(function(){
		jsLIB.resetForm( $('#cadPrepareForm') );
		populateMembers();
		$("#prepareModal").modal();
		ruleBotaoGerar();
	});
	
	$('#cadPrepareForm')
		.submit( function(e) {
			e.preventDefault();
			e.stopPropagation();
		})
	;

	$('[tab-function]').on('click', function(event){
		var objOrigem = $(this);
		if ( objOrigem.attr("tab-function") == "radio" ) {
			$('[tab-function=radio]').each(function(){
				$( '#'+$(this).attr('for') ).visible(false);
			});			
		}
		$( '#'+objOrigem.attr('for') ).visible( !objOrigem.hasClass("active") );
		ruleBotaoGerar( true );
	});

	$("#btnGerar").click( function(e) {
		var parameter = {
			frm: jsLIB.getJSONFields( $('#cadPrepareForm') )
		};
		getFunctions( parameter );
		
		var url = jsLIB.rootDir+'report/geraOcorrencias.php';
		if ( parameter.frm.id ) {
			url += '?id='+ parameter.frm.id.toString();
		} else if ( parameter.frm.ip ) {
			url += '?ip='+ parameter.frm.ip.toString();
		}
		window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
		
		$("#prepareModal").modal('hide');
	});
});

function getFunctions(parameter){
	var retorno = {};
	var lista = false;
	$("[name=quem].active").each( function() {
		var id = $(this).attr('for');
		if (id == "Lista"){
			lista = true;
			var selected = ocoDataTable.rows( { filter : 'applied'} ).data();
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

function buttonsPrimary(){
	$("#btnPrepare").visible( ocoDataTable.page.info().recordsDisplay > 0 );
}

function ruleBotaoGerar( force ){
	$("#btnGerar").visible( force == true || $("[name=quem].active").length > 0 );
}

function buttons(){
	$('#btnDel').visible( $("#ocoID").val() != "Novo" && valuePend == 'S' && valuePendOrig == 'S' );
	$("#btnGravar").visible(valuePendOrig !== 'N' && $("#ocoDH").val() != '' && $("#cmNome").val() != '');
}

function rulefields(){
	$("#ocoDH").enable(valuePendOrig !== 'N');
	$("#tpOcor").prop('disabled', (valuePendOrig == 'N') ).change();
	$("#fgPend").prop('disabled', (valuePendOrig == 'N') ).change();
	tinymce.get('txt').setMode(valuePendOrig == 'N'?'readonly':'design');
}

function populateOcorrencia( ocorrenciaID ) {
	jsLIB.ajaxCallOld( false, jsLIB.rootDir+"rules/ocorrencias.php", { MethodName : 'fOcorrencia', data : { id : ocorrenciaID } }, function(oc){
		jsLIB.populateOptions( $("#cmNome"), oc.nomes );
		jsLIB.populateForm( $("#cadOcoForm"), oc.ocorrencia );
		valuePendOrig = oc.ocorrencia.fg_pend;
		valuePend = jsLIB.getValueFromField( $("#fgPend") );
		rulefields();
		buttons();
	});
}

function refreshAndButtons(){
	ocoDataTable.ajax.reload( function(){
		buttonsPrimary();
	});
}

function populateMembers(){
	jsLIB.ajaxCallOld( false, jsLIB.rootDir+"rules/ocorrencias.php", { MethodName : 'fGetMembros' }, function(oc){
		jsLIB.populateOptions( $("#cmName"), oc.nomes );
	});
}

function updateOcorrencia(){
	var parameter = {
		op: "UPDATE",
		frm: jsLIB.getJSONFields( $('#cadOcoForm') )
	};
	jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/ocorrencias.php", { MethodName : 'fOcorrencia', data : parameter }, function(oc){
		$("#ocoID").val(oc.id);
		valuePendOrig = oc.so;
		valuePend = jsLIB.getValueFromField( $("#fgPend") );
		buttons();
		rulefields();
		refreshAndButtons();
	});
}