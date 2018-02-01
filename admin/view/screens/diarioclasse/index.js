var diaDataTable = undefined;
var rowSelected = undefined;
var valuePend = undefined;
var valuePendOrig = undefined;
var formPopulated = false;
var tpFiltro = (jsLIB.parameters.flt === 'ALL' ? 'T' : 'Y');

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );

	diaDataTable = $('#diaDataTable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ registros",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"admin/rules/diarioClasse.php",
			data	: function (d) {
					d.MethodName = "getListaDiario",
					d.data = {
						filter: tpFiltro
					}
				},
			dataSrc: "diario"
		},
		order: [ 3, 'desc' ],
		columns: [
			{	data: 'id',
				visible: false
			},
			{	data: 'so',
				visible: false
			},
			{	data: 'sq',
				sortable: true,
				width: "5%"
			},
			{	data: 'cl',
				sortable: true,
				width: "30%"
			},
			{	data: 'rq',
				sortable: true,
				width: "47%"
			},
			{	data: 'dh',
				sortable: true,
				width: "5%",
				render: function (data) {
					return moment.unix(data).format("DD/MM")
				}
			},
			{	data: 'st',
				sortable: true,
				width: "10%",
				render: function (data) {
					return (data == 'S' ? "PLANEJADO" : "Concluído");
				}
			},
			{
				width: "3%",
                className: 'details-control dt-right',
                sortable: false,
                data: 'in',
				render: function (data, type, row) {
					return '<span class="badge badge-pill progress-bar-'+ data.cl +'">'+ data.pc +'</span>';
				}
            }
		],
		fnInitComplete: function(oSettings, json) {
		  buttonsPrimary();
		},
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if ( aData.st == 'S' ) {
				$(nRow.cells[4]).css('color', '#cc0000').css('font-weight', 'bold');
			}
        }
	});

	$('#datetimepicker')
		//.datetimepicker({
		//	locale: 'pt-br',
		//	language: 'pt-BR',
		//	format: 'DD/MM/YYYY',
		//	maskInput: true,
		//	pickDate: true,
		//	pickTime: false,
		//	pickSeconds: false,
		//	useCurrent: false
		//})
		.on('dp.change',function(){
			$("#cadRegForm").formValidation('revalidateField', 'regDH');
			buttons();
		});

	$('#btnAtivos, #btnTodos').click(function(){
		switchSelecion( $(this).attr('tp-filtro') );
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

	//$("#nrCopias").TouchSpin({
	//	verticalbuttons: true,
	//	verticalupclass: 'glyphicon glyphicon-plus',
	//	verticaldownclass: 'glyphicon glyphicon-minus'
	//});

	$('#diaDataTable tbody')
		.on('click', 'tr', function (e) {
			if ($(this).attr('class')){
				rowSelected = this;
				valuePendOrig = diaDataTable.row( rowSelected ).data().so;
				populateRegistro( diaDataTable.row( rowSelected ).data().id );
				$("#diaModal").modal();
			}

		}).on('click', 'td.details-control', function (e) {
			rowSelected = this.parentNode;
			e.stopPropagation();
			e.preventDefault();

			var row = diaDataTable.row( rowSelected );
			if ( row.child.isShown() ) {
				row.child.hide();
			} else {
				jsLIB.ajaxCall({
					type: "GET",
					url: jsLIB.rootDir+"admin/rules/diarioClasse.php",
					data: { MethodName : 'fDetalheItem', data : { filter: tpFiltro, id: row.data().id, cl: row.data().in.cl } },
					success: function(oc){
						row.child( oc ).show();
					}
				});

			}
		});

	$("#cadRegForm")
		.on("change", "[field]", function(e) {
			$("#cadRegForm")
				.formValidation('revalidateField', this.id);
		})
		.on('err.field.fv', function(e, data) {
			$('#btnGravar').visible(false);
		})
		.formValidation({
			framework: 'bootstrap',
			fields: {
				regDH: {
					validators: {
						excluded: false,
						notEmpty: {
							message: 'A data n&atilde;o pode estar em branco!'
						},
						date: {
							format: 'DD/MM/YYYY',
							message: 'Data inv&aacute;lida!'
						}
					}
				},
				cmClasse: {
					validators: {
						notEmpty: {
							message: 'Selecione a Classe'
						}
					}
				},
				cmReq: {
					validators: {
						notEmpty: {
							message: 'O Requisito não pode ficar em branco!'
						}
					}
				},
				cmRef: {
					validators: {
						notEmpty: {
							message: 'Este Item não pode ficar em branco!'
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
			message: 'Confirma exclus&atilde;o deste registro?',
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
							id: $('#regID').val(),
							op: "DELETE"
						};
						jsLIB.ajaxCall({
							waiting : true,
							url: jsLIB.rootDir+"admin/rules/diarioClasse.php",
							data: { MethodName : 'fRegistro', data : parameter },
							success: function(){
								refreshAndButtons();
								dialogRef.close();
								$("#diaModal").modal('hide');
							}
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
				message: 'Confirma conclus&atilde;o dessa atividade?',
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
							updateRegistro();
							updateNotifications();
							refreshAndButtons();
							dialogRef.close();
						}
					}
					]
			});
		} else {
			updateRegistro();
			refreshAndButtons();
		}
	});

	$("#diaModal").on('show.bs.modal', function(event){
		buttons();
	});

	$('#btnNovo').click(function(){
		jsLIB.resetForm( $('#cadRegForm') );
		$('#divReferencia').visible( false );
		populateRegistro( $("#regID").val("Novo").val() );
		buttons();
		$("#diaModal").modal();
	});

	$("#fgPend").change(function(){
		valuePend = jsLIB.getValueFromField( $("#fgPend") );
		buttons();
	});

	$("#cmClasse").change(function(){
		populateReqs();
		buttons();
	});

	$("#cmReq").change(function(){
		if (this.options.selectedIndex > 0){
			$("#cadRegForm").formValidation('revalidateField', "cmRef");
			populateRefs();
			buttons();
		}
	});

	$("#cmRef").change(function(){
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

		var url = jsLIB.rootDir+'report/geraDiarioClasse.php';
		if ( parameter.frm.id ) {
			url += '?id='+ parameter.frm.id.toString();
		} else if ( parameter.frm.ip ) {
			url += '?ip='+ parameter.frm.ip.toString();
		}
		window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);

		$("#prepareModal").modal('hide');
	});
});

function ruleButtonSelection( filtro ){
	if ( filtro == 'Y' && !$('#btnAtivos').hasClass("btn-primary") ) {
		$('#btnAtivos').removeClass("btn-primary-outline").addClass("btn-primary");
		$('#btnTodos').removeClass("btn-primary").addClass("btn-primary-outline");
	} else if ( filtro == 'T' && !$('#btnTodos').hasClass("btn-primary") ) {
		$('#btnTodos').removeClass("btn-primary-outline").addClass("btn-primary");
		$('#btnAtivos').removeClass("btn-primary").addClass("btn-primary-outline");
	}
}

function switchSelecion( filtro ) {
	tpFiltro = filtro;
	ruleButtonSelection( filtro );
	refreshAndButtons();
}

function getFunctions(parameter){
	var retorno = {};
	var lista = false;
	$("[name=quem].active").each( function() {
		var id = $(this).attr('for');
		if (id == "Lista"){
			lista = true;
			var selected = diaDataTable.rows( { filter : 'applied'} ).data();
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
	$("#btnPrepare").visible( diaDataTable.page.info().recordsDisplay > 0 );
}

function ruleBotaoGerar( force ){
	$("#btnGerar").visible( force == true || $("[name=quem].active").length > 0 );
}

function buttons(){
	$('#btnDel').visible( $("#regID").val() != "" && valuePend == 'S' && valuePendOrig == 'S' );
	var ref = ( ($('#divReferencia:visible').is(":visible") && $("#cmRef").val() != '') || !$('#divReferencia:visible').is(":visible") ) ;
	$("#btnGravar").visible(
		valuePendOrig !== 'N' &&
		$("#regDH").val() != '' &&
		$("#cmClasse").val() != '' &&
		$("#cmReq").val() != '' &&
		ref
	);
}

function rulefields(){
	$("#regDH").enable(valuePendOrig !== 'N');
	$("#tpOcor").prop('disabled', (valuePendOrig == 'N') ).change();
	$("#fgPend").prop('disabled', (valuePendOrig == 'N') ).change();
	tinymce.get('txt').setMode(valuePendOrig == 'N'?'readonly':'design');
}

function populateRegistro( diarioID ) {
	formPopulated = false;
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"admin/rules/diarioClasse.php",
		data: { MethodName : 'fRegistro', data : { id : diarioID } },
		success: function(oc){
			if (oc){
				jsLIB.populateOptions( $("#cmClasse"), oc.classe );
				jsLIB.populateOptions( $("#cmReq"), oc.req );
				rulesRefs(oc.ref);
				jsLIB.populateForm( $("#cadRegForm"), oc.diario );
				valuePendOrig = oc.diario.fg_pend;
			}
			valuePend = jsLIB.getValueFromField( $("#fgPend") );
			rulefields();
			buttons();
			formPopulated = true;
		}
	});
}

function refreshAndButtons(){
	diaDataTable.ajax.reload( function(){
		buttonsPrimary();
	});
}

function populateMembers(){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"admin/rules/diarioClasse.php",
		data: { MethodName : 'fGetMembros' },
		success: function(oc){
			jsLIB.populateOptions( $("#cmName"), oc.nomes );
		}
	});
}

function populateReqs(){
	var classeID = $("#cmClasse").val();
	if (formPopulated && classeID){
		jsLIB.ajaxCall({
			type: "GET",
			url: jsLIB.rootDir+"admin/rules/diarioClasse.php",
			data: {
				MethodName : 'fGetCompl',
				data : {
					id_classe: classeID
				}
			},
			success: function(cm){
				jsLIB.populateOptions( $("#cmReq"), cm.req );
				$("#seqID").val(cm.sq);
			}
		});
	}
}

function populateRefs(){
	var selectedOption = $('#cmReq option:selected');
	if (formPopulated && selectedOption.attr("tp") == 'E'){
		var parameter = {
			id_req: $("#cmReq").val()
		};
		jsLIB.ajaxCall({
			type: "GET",
			url: jsLIB.rootDir+"admin/rules/diarioClasse.php",
			data: { MethodName : 'fGetRef', data : parameter },
			success: function(cm){
				rulesRefs(cm);
			}
		});
	}
}

function rulesRefs(cm){
	$('#divReferencia').visible( false );
	if (cm){
		jsLIB.populateOptions( $("#cmRef"), cm );
		if (cm.length == 1){
			$("#cmRef").selectpicker('val',cm[0].id);
		} else {
			$('#divReferencia').visible( true );
		}
	}
}

function updateRegistro(){
	var parameter = {
		op: "UPDATE",
		frm: jsLIB.getJSONFields( $('#cadRegForm') )
	};
	jsLIB.ajaxCall({
		waiting: true,
		url: jsLIB.rootDir+"admin/rules/diarioClasse.php",
		data: { MethodName : 'fRegistro', data : parameter },
		success: function(oc){
			$("#regID").val(oc.id);
			valuePendOrig = oc.so;
			valuePend = jsLIB.getValueFromField( $("#fgPend") );
			buttons();
			rulefields();
			refreshAndButtons();
		}
	});
}
