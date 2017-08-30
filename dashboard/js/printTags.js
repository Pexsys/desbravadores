var dataTable = undefined;

$(document).ready(function(){

	dataTable = $('#tagDatatable').DataTable({
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
			url	: jsLIB.rootDir+"rules/printTags.php",
			data	: function (d) {
					d.MethodName = "getTags",
					d.data = { 
							 filtro: 'T',
							 filters: jsFilter.jSON()
						}
				},
			dataSrc: "tags"
		},
		fnInitComplete: function(oSettings, json) {
		  buttons();
		},
		columns: [
			{	data: "id",
				visible: false
			},
			{	data: "md",
				visible: false
			},
			{	data: "nm",
				type: 'ptbr-string',
				width: "60%"
			},
			{	data: "tp",
				type: 'ptbr-string',
				width: "40%"
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
	}).order( [ 2, 'asc' ], [ 3, 'asc' ] );
	
	$('#tagDatatable tbody').on('click', 'tr', function () {
		$(this).toggleClass('selected');
		ruleBtnDelete();
	});

	$("#addTagsForm")
		.submit( function(e) {
			e.preventDefault();
			e.stopPropagation();
			updateSaida();
		})
	;
	
	$('#btnNovo').click(function(){
		jsLIB.resetForm( $('#addTagsForm') );
		populateMembers('A');
		jsFilter.removeAll();
		$("#tagsModal").modal();
	});
	
	$('#btnDel').click(function(){
		modalDelete('LINES','Confirma exclus&atilde;o das linhas selecionadas da fila de impress&atilde;o?');
	});

	$('#btnClear').click(function(){
		modalDelete('ALL','Confirma exclus&atilde;o total da fila de impress&atilde;o?');
	});

	$("#cbNomes").on("reload.options.bs.select", function(event){
		populateFilters();
	});

	$('#cbTags').on("change",function(e) {
		var aValues = $(this).val();
		if ( aValues == null ){
			$("#divAprend").visible(false);
			$("#cbTags").selectpicker('deselectAll');
			$($(this.options)).each(function(){
				$(this).attr('disabled', false);
			});
		} else {
			var attrCL = this.options[this.options.selectedIndex].getAttribute('cl');
			var visible = (attrCL == "S");
			$("#divAprend").visible( visible );
			if ( visible ) {
				var parameters = {
					filtro: aValues
				}	
				var sd = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/printTags.php", { MethodName : 'getClasse', data : parameters }, 'RETURN' );
				jsLIB.populateOptions( $("#cbAprend"), sd );
			}
			$($(this.options).not("[cl="+attrCL+"]")).each(function(){
				$(this).attr('disabled', true);
			});
		}
		$("#cbTags").selectpicker('refresh');
	});
	
	$('#cbAprend').on("change", function(e){
		$($(this.options)).each(function(){
			var value = $(this).val() * 1;
			var valPar = ((value % 2) == 0);
			if (valPar){
				$("#cbAprend option[value='"+(value-1)+"']").attr('disabled', this.selected);
			} else {
				$("#cbAprend option[value='"+(value+1)+"']").attr('disabled', this.selected);
			}
		});
		$("#cbAprend").selectpicker('refresh');
	});
	
	$("#addList").click(function(){
		updateSaida();
	});
	
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
	
	$('#btnPrepare').click(function(){
		jsLIB.resetForm( $('#cadPrepareForm') );
		populateMembers('P');
		$("#prepareModal").modal();
	});
	
	$('#cadPrepareForm')
		.submit( function(e) {
			e.preventDefault();
			e.stopPropagation();
		})
	;

	$("#btnGerar").click( function(e) {
		var parameter = {
			frm: jsLIB.getJSONFields( $('#cadPrepareForm') )
		};
		getFunctions( parameter );
		
		var url = jsLIB.rootDir+'report/geraEtiquetas.php?md='+$("#cmForm").val();
		if ( parameter.frm.id ) {
			url += '&id='+ parameter.frm.id.toString();
		} else if ( parameter.frm.ip ) {
			url += '&ip='+ parameter.frm.ip.toString();
		}
		var pg = undefined;
		$('[name=pages]').each(function(){
			if (pg == undefined){
				pg = '';
			} else {
				pg += ',';
			}
			$(this).find("input[type=checkbox]").each(function(){
				if ( $(this).prop('checked') ) {
					pg += $(this).attr('value-on');
				} else {
					pg += $(this).attr('value-off');
				}
			});
		});
		if ( pg ){
			url += '&pg='+pg;
		}
		window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
		
		$("#prepareModal").modal('hide');
	});
	
	$('#cmForm').on("change", function(e){
		var fi = this.options[this.options.selectedIndex].getAttribute('fi');
		var visible = (fi == "S");
		$("#divPageControl").visible( visible );
		$("#divIncomplete").attr('qt-sq',0).empty();
		ruleBotaoGerar();
	});	
	
	$('#btnAddFI').click(function(e){
		var sq = ($("#divIncomplete").attr('qt-sq')*1)+1;
		var strAdd = '<div class="col-xs-4" name="pages" id="page-'+sq+'">';
		strAdd += '<div class="panel panel-default">';
		strAdd += '<div class="panel-heading" style="padding:4px 10px 0px">';
		strAdd += '<label title-page="S">P&aacute;gina '+sq+'</label>';
		strAdd += '<button class="btn btn-danger btn-xs pull-right" name="btnDelFI" for="page-'+sq+'">&times;</button>';
		strAdd += '</div>';
		strAdd += '<div class="panel-body" style="padding:4px 10px 10px">';
		strAdd += '<div class="row">';
		var qt = $("#cmForm option[value='"+$("#cmForm").val()+"']").attr('qt');
		for (var i=0;i<qt;i++){
			strAdd += '<div class="col-xs-6">';
			strAdd += '<input type="checkbox" checked value-on="S" value-off="N" data-on="<b>'+(i+1)+'</b>" data-off="'+(i+1)+'" data-onstyle="success" data-offstyle="secondary" data-toggle="toggle" data-width="40" data-size="mini" data-style="quick"/>';
			strAdd += '</div>';
		}
		strAdd += '</div>';
		strAdd += '</div>';
		strAdd += '</div>';
		strAdd += '</div>';
		strAdd = $(strAdd);
		strAdd.find("input[type=checkbox]").bootstrapToggle();
		strAdd.find('[name=btnDelFI]').on('click', function(e){
			var sq = ($("#divIncomplete").attr('qt-sq')*1)-1;
			$("#divIncomplete").attr('qt-sq',sq);
			$("#"+$(this).attr('for')).remove();
			var rn = 0;
			$('[name=pages]').each(function(){
				$(this).find("label[title-page=S]").html('P&aacute;gina '+(++rn));
			});
		});
		$("#divIncomplete").append(strAdd).attr('qt-sq',sq);
	});
	
	buttons();
});

function ruleBotaoGerar( force ){
	$("#btnGerar").visible( $("#cmForm").val() != '' && (force == true || $("[name=quem].active").length > 0) );
}

function getFunctions(parameter){
	var retorno = {};
	var lista = false;
	$("[name=quem].active").each( function() {
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

function modalDelete(sAct,sMsg){
	BootstrapDialog.show({
		title: 'Alerta',
		message: sMsg,
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

					var tmp = [];
					if (sAct == "LINES"){
						var selected = dataTable.rows('.selected').data();
						for (var i=0;i<selected.length;i++){
							tmp.push(selected[i].id);
						}
					}
					var parameter = {
						action: sAct,
						ids: tmp
					};
					jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/printTags.php", { MethodName : 'delete', data : parameter } );
					dialogRef.close();
					refreshAndButtons();
				}
			}
		]
	});
}

function refreshAndButtons(){
	dataTable.ajax.reload( function(){
		buttons();
	});
}

function ruleBtnDelete( force ){
	$("#btnDel").visible( force != undefined ? force : dataTable.rows('.selected').data().length > 0 );
}

function buttons(){
	$("#btnPrepare, #btnPage, #btnClear").visible( dataTable.page.info().recordsDisplay > 0 );
	ruleBtnDelete(false);
}

function populateFilters(){
	var parameters = { 
		filtro: 'A',
		filters: jsFilter.jSON()
	}	
	var mb = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/printTags.php", { MethodName : 'getMembrosFilter', data : parameters }, 'RETURN' );
	if ( mb.filter && mb.filter.length > 0 ) {
		$("#cbNomes").selectpicker('deselectAll');
		$("#cbNomes").selectpicker('val', mb.filter);
	} else {
		$("#cbNomes").selectpicker('selectAll');
	}
}

function populateMembers( tpDialog ) {
	var parameters = {
		filtro: tpDialog
	}	
	var sd = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/printTags.php", { MethodName : 'getData', data : parameters }, 'RETURN' );
	if (tpDialog == 'A') {
		jsLIB.populateOptions( $("#cbTags"), sd.tags );
		jsLIB.populateOptions( $("#cbNomes"), sd.membros );
		$("#cbNomes").selectpicker('selectAll');
	} else if (tpDialog == 'P') {
		jsLIB.populateOptions( $("#cmForm"), sd.forms );
		jsLIB.populateOptions( $("#cmNome"), sd.membros );
		$("#cmNome").selectpicker('selectAll');
	}
}

function updateSaida(){
	var parameter = {
		frm: jsLIB.getJSONFields( $('#addTagsForm') )
	};
	var sd = jsLIB.ajaxCall( false, jsLIB.rootDir+"rules/printTags.php", { MethodName : 'addTags', data : parameter }, 'RETURN' );
	refreshAndButtons();
}