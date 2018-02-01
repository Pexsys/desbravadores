var saidaDataTable = undefined;
var attrDataTable = undefined;
var rowSelected = undefined;
var tpFiltro = (jsLIB.parameters.flt === 'ALL' ? 'T' : 'Y');
var tpFiltroAttr = '';

$(document).ready(function(){
	$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm' );

	attrDataTable = $('#attrDatatable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ participantes",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"admin/rules/saidas.php",
			data	: function (d) {
					d.MethodName = "getAttrib",
					d.data = {
						filter: tpFiltroAttr,
						id: $("#saidaID").val()
					}
				},
			dataSrc: "people"
		},
		order: [ 1, 'asc' ],
		columns: [
			{	data: 'id',
				visible: false
			},
			{	data: 'nm',
				sortable: true,
				type: 'ptbr-string',
				width: "55%"
			},
			{	data: 'un',
				sortable: true,
				type: 'ptbr-string',
				width: "35%"
			},
			{	data: 'cd',
				sortable: true,
				type: 'ptbr-string',
				width: "5%",
				render: function(data, type, full, meta){
					return "<input type=\"text\" name=\"editCD\" class=\"form-control input-xs\" attr-id=\""+full.id+"\" value=\""+ data +"\">";
				}

			}
		]
	});


	saidaDataTable = $('#saidasDatatable').DataTable({
		lengthChange: false,
		ordering: true,
		paging: false,
		scrollY: 300,
		searching: true,
		processing: true,
		language: {
			info: "_END_ sa&iacute;das",
			search: "",
			searchPlaceholder: "Procurar...",
			infoFiltered: " de _MAX_",
			loadingRecords: "Aguarde - carregando...",
			zeroRecords: "Dados indispon&iacute;veis para esta sele&ccedil;&atilde;o",
			infoEmpty: "0 encontrados"
		},
		ajax: {
			type	: "GET",
			url	: jsLIB.rootDir+"admin/rules/saidas.php",
			data	: function (d) {
					d.MethodName = "getSaidas",
					d.data = {
						filter: tpFiltro
					}
				},
			dataSrc: "saidas"
		},
		order: [ 3, 'desc' ],
		columns: [
			{	data: 'id',
				sortable: true,
				width: "5%"
			},
			{	data: 'ds',
				sortable: true,
				width: "29%"
			},
			{	data: 'dst',
				sortable: true,
				width: "40%"
			},
			{	data: 'dh_s',
				sortable: true,
				width: "13%",
				render: function (data) {
					return moment.unix(data).format("DD/MM/YYYY HH:mm")
				}
			},
			{	data: 'dh_r',
				sortable: true,
				width: "13%",
				render: function (data) {
					return moment.unix(data).format("DD/MM/YYYY HH:mm")
				}
			}
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            if ( aData.dh_r < moment().unix() ) {
                $('td', nRow).css('color', '#d0d0d0');
            }
        }
	});

	//$('#datetimepickerini, #datetimepickerfim').datetimepicker({
	//	locale: 'pt-br',
	//	language: 'pt-BR',
	//	format: 'DD/MM/YYYY HH:mm',
	//	maskInput: true,
	//	pickDate: true,
	//	pickTime: true,
	//	pickSeconds: false,
	//	useCurrent: false
	//});

	$('#btnAtivos, #btnTodos').click(function(){
		 switchSelecion( $(this).attr('tp-filtro') );
	});

	$('#datetimepickerini')
		.on("dp.change", function (e) {
			$('#cadSaidasForm').formValidation('revalidateField', 'dh_s');
		})
		.on("dp.show", function(e){
			$('#datetimepickerfim').data("DateTimePicker").hide();
		})
		.click(function(e){
			$('#datetimepickerfim').data("DateTimePicker").hide();
		});

	$('#datetimepickerfim')
		.on("dp.change", function(e){
			$('#cadSaidasForm').formValidation('revalidateField', 'dh_r');
		})
		.on("dp.show", function(e){
			$('#datetimepickerini').data("DateTimePicker").hide();
		})
		.click(function(e){
			$('#datetimepickerini').data("DateTimePicker").hide();
		});

	$('#saidasDatatable tbody').on('click', 'tr', function () {
		rowSelected = this;
		populateSaida( saidaDataTable.row( rowSelected ).data().id );
		$("#saidasModal").modal();
	});

	$("#cadSaidasForm")
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
			updateSaida();
			buttons();
		})
	;

	$("#printForm")
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
			event.preventDefault();
			var opt = $("#printModal").attr('report-id');
			if (opt){
				url = jsLIB.rootDir+'report/'+opt+'&eve='+$("#saidaID").val();
				window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
			}
		})
	;

	$("[name=cmLista]").change(function(){
		rulesGeracao( $(this), true );
	});

	$("[name=cmSubLista]").change(function(){
		rulesGeracao( $(this), false );
	});

	$('#btnPrint').click(function(){
		jsLIB.resetForm( $('#printForm') );
		$("#printModal").modal();
		$("[name=cmLista]").triggerHandler('change');
	});

	$('#btnUse').click(function(){
	});

	$("#saidasModal").on('show.bs.modal', function(event){
		buttons();
	});

	$('#btnNovo').click(function(){
		jsLIB.resetForm( $('#cadSaidasForm') );
		populateSaida( $("#saidaID").val("Novo").val() );
		$("#saidasModal").modal();
	});

	$('#btnDel').click(function(){
		BootstrapDialog.show({
			title: 'Alerta',
			message: 'Confirma exclus&atilde;o deste evento e autoriza&ccedil;&otilde;es?',
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
							id: $('#saidaID').val(),
							op: "DELETE"
						};
						jsLIB.ajaxCall({
							waiting : true,
							url: jsLIB.rootDir+"admin/rules/saidas.php",
							data: { MethodName : 'fSaida', data : parameter },
							success: function(data){
								saidaDataTable.ajax.reload( function(){
									dialogRef.close();
									$("#saidasModal").modal('hide');
								});
							}
						});
					}
				}
			]
		});
	});

	$("#cbParticip").on("reload.options.bs.select", function(event){
		populateMembers();
	});

	$("[name=btnShowAttr]").click(function(){
		var attrRule = $(this).attr("attr-rule");
		$("#lblTitle").html(this.innerHTML);
		$("#lblRow").html($(this).attr("attr-caption"));

		$("#attrModal")
			.attr("attr-rule", attrRule)
			.modal()
		;
		tpFiltroAttr = attrRule;
		attrDataTable.ajax.reload(function(){
			$("[name=editCD]").unbind('change').change(function(){
				var parameter = {
					id: $(this).attr('attr-id'),
					fl: attrRule,
					vl: $(this).val()
				};
				jsLIB.ajaxCall({
					url: jsLIB.rootDir+"admin/rules/saidas.php",
					data: { MethodName : 'setAttrib', data : parameter }
				});
			});

		});
	});

});

function rulesGeracao( obj, filter ){
	if (filter){
		$("#divFilterPrint").visible(false);
		$("[name=rowFilter]").visible(false);
	}
	var show = obj.find(":selected").attr('show');
	if (show !== undefined) {
		if (filter){
			$("#divFilterPrint").visible(true);
			$("#"+show).visible(true);
			$("[name=cmSubLista]").filter(":visible").triggerHandler('change');
		}
	} else if (obj.val() !== ''){
		$("#printModal").attr('report-id', obj.val() );
	}
}

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
	saidaDataTable.ajax.reload();
}

function buttons(){
	$('#divAttr').visible( $("#saidaID").val() != "Novo" );
	$('#btnDel').visible( $("#saidaID").val() != "Novo" );
	$('#btnPrint').visible( $("#saidaID").val() != "Novo" );
}

function populateMembers(){
	var parameters = {
		filtro: 'Y',
		dhr: $('#dhRetorno').val(),
		id:  $("#saidaID").val(),
		filters: jsFilter.jSON()
	}
	jsLIB.ajaxCall({
		waiting : true,
		type: "GET",
		url: jsLIB.rootDir+"admin/rules/saidas.php",
		data: { MethodName : 'getMembrosFilter', data : parameters },
		success: function(mb){
			jsLIB.populateOptions( $("#cbParticip"), mb.membros );
			if ( mb.filter && mb.filter.length > 0 ) {
				$("#cbParticip").selectpicker('deselectAll');
				$("#cbParticip").selectpicker('val', mb.filter);
			} else {
				$("#cbParticip").selectpicker('selectAll');
			}
		}
	});
}

function populateSaida( saidaID ) {
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"admin/rules/saidas.php",
		data: { MethodName : 'fSaida', data : { id : saidaID } },
		success: function(sd){
			jsLIB.populateForm( $("#cadSaidasForm"), sd.saida );
			jsLIB.populateOptions( $("#cbParticip"), sd.membros );
			var filterArray = $.grep(sd.membros, function(e){ return e.pt == 'S'; });
			if ( !filterArray || filterArray.length == 0 ) {
				$("#cbParticip").selectpicker('selectAll');
			}
		}
	});
}

function updateSaida(){
	var parameter = {
		op: "UPDATE",
		frm: jsLIB.getJSONFields( $('#cadSaidasForm') )
	};
	jsLIB.ajaxCall({
		waiting : true,
		url: jsLIB.rootDir+"admin/rules/saidas.php",
		data: { MethodName : 'fSaida', data : parameter },
		success: function(sd){
			$("#saidaID").val(sd.id);
			buttons();
			saidaDataTable.ajax.reload();
		}
	});
}
