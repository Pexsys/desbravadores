function showDetailEspec( objDetail, parameters ){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/painelAprendizado.php",
		data: { MethodName : 'getEspec', data : parameters },
		callBackSucess: function(espec){
			if (espec.detail){
				$(objDetail).html(espec.detail);
			}
		}
	});
}

function showDetailEspecPeople( objDetail, parameters ){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/painelAprendizado.php",
		data: { MethodName : 'getEspecPeople', data : parameters },
		callBackSucess: function(espec){
			if (espec.people){
				$(objDetail).html(espec.people);
			}
		}
	});
}

function showDetailClass( objDetail, parameters ){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/painelAprendizado.php",
		data: { MethodName : 'getClasses', data : parameters },
		callBackSucess: function(classes){
			if (classes.detail){
				$(objDetail).html(classes.detail);
			}
		}
	});
}

function showDetailClassReq( objDetail, parameters ){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/painelAprendizado.php",
		data: { MethodName : 'getPendentes', data : parameters },
		callBackSucess: function(detail){
			if (detail.pend){
				$(objDetail).html(detail.pend);
			}
		}
	});
}