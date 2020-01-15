$(document).ready(function(){
	//FORM
	$("#change-password")
        .on('init.field.fv', function(e, data) {
            // data.fv      --> The FormValidation instance
            // data.field   --> The field name
            // data.element --> The field element
            var $parent = data.element.parents('.form-group'),
                $icon   = $parent.find('.form-control-feedback[data-fv-icon-for="' + data.field + '"]');
            // You can retrieve the icon element by
            // $icon = data.element.data('fv.icon');
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
				psw: {
					validators: {
						notEmpty: {
							message: 'A senha &eacute; obrigat&oacute;ria'
						},
						stringLength: {
							min: 7,
							max: 30,
							message: 'Sua senha deve conter 7 e 30 caracteres'
						}
					}
				},
				repeat: {
					validators: {
						notEmpty: {
							message: 'A senha &eacute; obrigat&oacute;ria'
						},
						stringLength: {
							min: 7,
							max: 30,
							message: 'Sua senha deve conter 7 e 30 caracteres'
						},
                        identical: {
                            field: 'psw',
                            message: 'A confirmação está diferente da senha'
                        }
					}
				}
			}
		})
		
		.on('success.form.fv', function(e) {
            // Prevent form submission
            e.preventDefault();
        })	
	
		.submit( function() {
			var parameter = {
			    ...($("#btnChangePass").attr("id-item") !== '' ? { id: $("#btnChangePass").attr("id-item") } : {}),
				password: $.sha1(jQuery('#psw').val().toLowerCase())
			};
			jsLIB.ajaxCall({
				waiting : true,
				url: jsLIB.rootDir+'rules/login.php',
				data: { MethodName : 'changePassword', data : parameter },
				success: function(data){
					if ( data.changed == true ) {
						changeError('Senha alterada com Sucesso!', { type: BootstrapDialog.TYPE_SUCCESS, title: 'Ok', cssClass: 'btn-success' });
					} else {
				    changeError('Erro', { type: BootstrapDialog.TYPE_DANGER, title: 'Erro', cssClass: 'btn-danger' });
					}
				},
				error: function(data){
				    changeError('Erro', { type: BootstrapDialog.TYPE_DANGER, title: 'Erro', cssClass: 'btn-danger' });
				},
			});
		});

});

function changeError(message, { type = BootstrapDialog.TYPE_DANGER, title = 'Erro', cssClass = 'btn-danger' }){
	BootstrapDialog.show({
		title,
		message,
		type,
		size: BootstrapDialog.SIZE_SMALL,
		draggable: true,
		closable: true,
		closeByBackdrop: false,
		closeByKeyboard: false,
		buttons: [{
			label: 'Fechar',
			cssClass,
			action: function(dialogRef){
				dialogRef.close();
				$("#comModal").modal('hide');
			}
		}]	
	});	
}