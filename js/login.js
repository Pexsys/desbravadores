$(document).ready(function(){

	$("#login-form")
		.formValidation({
			framework: 'bootstrap',
	        excluded: ':disabled',
	        row: {
	            valid: 'success',
	            invalid: 'error'
	        },
	        icon: {
	            valid: null,
	            invalid: null,
	            validating: null
	        },
			fields: {
				usr: {
					validators: {
						notEmpty: {
							message: 'O c&oacute;digo do usu&aacute;rio &eacute; obrigat&oacute;rio'
						},
						stringLength: {
							min: 7,
							max: 30,
							message: 'O c&oacute;digo do usu&aacute;rio deve ter entre 7 e 30 caracteres'
						},
						regexp: {
							regexp: new RegExp("^[a-zA-Z0-9.]+$"),
							message: 'O c&oacute;digo do usu&aacute;rio s&oacute; pode conter letras, o ponto, e n&uacute;meros'
						}
					}
				},
				psw: {
					validators: {
						notEmpty: {
							message: 'A senha &eacute; obrigat&oacute;ria'
						},
						stringLength: {
							min: 1,
							max: 30,
							message: 'Sua senha deve conter 1 e 30 caracteres'
						},
						regexp: {
							regexp: new RegExp("^[a-zA-Z0-9.]+$"),
							message: 'O c&oacute;digo do usu&aacute;rio s&oacute; pode conter letras, n&uacute;meros e . (ponto)'
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
			var $loading = $('.login-box').waitMe({
				effect: 'rotation',
				text: 'Verificando dados...',
				bg: 'rgba(255,255,255,0.90)',
				color: '#03A9F4'
			});
			var parameter = {
				page: $('#page').val(),
				username: $("#usr").val(),
				password: $.sha1($("#psw").val().toLowerCase())
			};
			jsLIB.ajaxCall({
				waiting : true,
				url: jsLIB.rootDir+'rules/login.php',
				data: { MethodName : 'login', data : parameter },
				success: data => {
					if ( data.login == true ) {
						window.location.replace(data.page);
					} else {
						loginError();
						$loading.waitMe('hide');
					}
				},
				error: () => {
					loginError();
					$loading.waitMe('hide');
				}
			});
		});

	$("#myBtnLogin").click(function(){
		$("#myLoginModal").modal();
	});

	$("#myBtnLogout").click(function(){
		jsLIB.ajaxCall({
			waiting : true,
			url: jsLIB.rootDir+'rules/login.php',
			data: { MethodName : 'logout' },
			success: function(dt){
				window.location.replace( jsLIB.rootDir+'index.php' );
			}
		});
	});

});
function loginError( jqxhr, errorMessage ) {
	swal({
        title: "Alerta!",
		text: "<span style=\"color: #CC0000\">ACESSO NEGADO!<span>",
		confirmButtonColor: "#DD6B55",
        html: true,
        timer: 5000,
        showConfirmButton: true
    });
}
