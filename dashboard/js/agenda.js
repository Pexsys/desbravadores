var calendar = undefined;
var options = {};
addEvent = false;

$(document).ready(function(){

	$("#myCalendarForm")
        .on('init.field.fv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
			$icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
           	$icon.on('click.clearing', function() {
               	if ( $icon.hasClass('glyphicon-remove') ) {
                   	data.fv.resetField(data.element);
               	}
            });
        })
	.formValidation({
		framework: 'bootstrap',
		icon: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			dh_ini:		{ validators: {
                    notEmpty: {
                        message: 'Data/hora obrigat&oacute;ria'
                    },
                    date: {
                        format: 'DD/MM/YYYY hh:mm',
                        message: 'Data/Hora inv&aacute;lida. Utilize o formato DD/MM/AAAA hh:mm'
                    }
                }
            },
			dh_fim:		{ validators: {
                    notEmpty: {
                        message: 'Data/hora obrigat&oacute;ria'
                    },
                    date: {
                        format: 'DD/MM/YYYY hh:mm',
                        message: 'Data/Hora inv&aacute;lida. Utilize o formato DD/MM/AAAA hh:mm'
                    }
                }
            },
			dsLocal:	{	validators: {
					notEmpty: {
						message: 'Local do evento Obrigat&oacute;rio'
					}
				}
			},
			dsInfo:			{ validators: {} },
			dsLogra:			{ validators: {} },
			nrLog:			{ validators: {} },
			dsComp:			{ validators: {} },
			dsBai:			{ validators: {} },
			dsCid:			{ validators: {} },
			cmUF:			{ validators: {} },
			cmGrupo:			{ validators: {} },
			cmRegra:			{ validators: {} },
			cmUniforme:		{ validators: {} },
			cmInstrucao:		{ validators: {} },
			cmPub:			{ validators: {} },
			cmTPEve:			{ validators: {} }
		}
	})
	.on('success.form.fv', function(e) {
            e.preventDefault();
        })	
	.submit( function(event) {
		updateEventDB();
		$('#btnX').triggerHandler('click');
		calendar.view();
	});
		
	options = {
		view					: 'month',
		language				: 'pt-BR',
		modal					: "#events-modal", 
		modal_type				: 'ajax', 
		modal_title				: function(e) {
			if (!addEvent) {
				closeCollapseAll();
				$('#btnDelete').show();
				jsLIB.ajaxCall({
					type: "GET",
					url: jsLIB.rootDir+"rules/agenda.php",
					data: { MethodName : 'events', data : { id : e.id } },
					callBackSucess: function(ev){
						jsLIB.populateForm( $('#myCalendarForm'), ev.result[0].info );
					}
				});
				return 'Evento';
			}
			addEvent = false;
		},
		events_source			: function( pFrom, pTo ) {
			var parameter = {
				from: pFrom.toDateTime(),
				to: pTo.toDateTime()
			};
			var retorno = jsLIB.ajaxCall({
				type: "GET",
				url: jsLIB.rootDir+"rules/agenda.php",
				data: { MethodName : 'events', data : parameter }
			});
			return retorno.result;
		},
		tmpl_path				: jsLIB.rootDir+"dashboard/tmpls/",
		tmpl_cache				: false,
		display_week_numbers	: true,
		weekbox					: true,
		format12				: false,
		first_day				: 2,
		
		onAfterEventsLoad: function(events) {
			if (!events) {
				return;
			}
			var list = $('#eventlist');
			list.html('');

			$.each(events, function(key, val) {
				$(document.createElement('li'))
					.html('<a href="' + val.url + '">' + val.title + '</a>')
					.appendTo(list);
			});
		},
		onAfterViewLoad: function(view) {
			$('.page-header h4').text(this.getTitle());
			$('.btn-group button').removeClass('active');
			$('button[data-calendar-view="' + view + '"]').addClass('active');
		},
		classes: {
			months: {
				general: 'label'
			}
		}
	};
	
	calendar = $('#calendar').calendar(options);

	$('.btn-group button[data-calendar-nav]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.navigate($this.data('calendar-nav'));
		});
	});

	$('.btn-group button[data-calendar-view]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.view($this.data('calendar-view'));
		});
	});

	$('#events-modal .modal-header, #events-modal .modal-footer').click(function(e){
		e.preventDefault();
		e.stopPropagation();
	});
	
	$('.panel')
		.on('show.bs.collapse', function (e) {
			$(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
		})
		.on('hide.bs.collapse', function (e) {
			$(this).find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
		})
	;

	$('#datetimepickerini, #datetimepickerfim').datetimepicker({
		locale: 'pt-br',
		language: 'pt-BR',
		format: 'DD/MM/YYYY HH:mm',
		maskInput: true,
		pickDate: true,
		pickTime: true,
		pickSeconds: false,
		useCurrent: false
	});
	
	$('#datetimepickerini')
		.on("dp.change", function (e) {
			$('#myCalendarForm').formValidation('revalidateField', 'dh_ini');
		})
		.on("dp.show", function(e){
			$('#datetimepickerfim').data("DateTimePicker").hide();
		})
		.click(function(e){
			$('#datetimepickerfim').data("DateTimePicker").hide();
		});
	
	$('#datetimepickerfim')
		.on("dp.change", function(e){
			$('#myCalendarForm').formValidation('revalidateField', 'dh_fim');
		})
		.on("dp.show", function(e){
			$('#datetimepickerini').data("DateTimePicker").hide();
		})
		.click(function(e){
			$('#datetimepickerini').data("DateTimePicker").hide();
		});
	
	$('#btnX').click(function(){
		$("#events-modal").modal('hide');
	});

	$('#addEvent').click(function(){
		addEvent = true;
		$('#hTitle').html('Novo Evento');
		jsLIB.resetForm( $('#myCalendarForm') );
		$('#btnDelete').hide();
		closeCollapseAll();
		$("#events-modal").modal();
	});
	
	$('#btnDelete').click(function(){
		BootstrapDialog.show({
            title: 'Alerta',
            message: 'Confirma exclus&atilde;o deste evento?',
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
							id: $('#eventID').val(),
							op: "DELETE"
						};
						jsLIB.ajaxCall({
							waiting : true,
							url: jsLIB.rootDir+"rules/agenda.php",
							data: { MethodName : 'fEvent', data : parameter },
							callBackSucess: function(){
								dialogRef.close();
								closeAndRefresh();
							}
						});
					}
				}
			]
		});
	});
});

function updateEventDB(){
	var parameter = {
		op: "UPDATE",
		frm: jsLIB.getJSONFields( $('#myCalendarForm') )
	};
	jsLIB.ajaxCall({
		waiting : true,
		url: jsLIB.rootDir+"rules/agenda.php",
		data: { MethodName : 'fEvent', data : parameter },
		callBackSucess: function(){
			dialogRef.close();
			closeAndRefresh();
		}
	});
}

function closeAndRefresh(){
	$('#btnX').triggerHandler('click');
	calendar.view();
}

function closeCollapseAll() {
	$('#divInfo .collapse').collapse('hide');
	$('#divInfo .panel-heading').each( function() {
		$(this).removeClass('panel-collapsed');
		$(this).find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
	});
}