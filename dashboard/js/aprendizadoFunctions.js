function showDetailEspec( objDetail, parameters ){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/painelAprendizado.php",
		data: { MethodName : 'getEspec', data : parameters },
		success: function(espec){
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
		success: function(espec){
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
		success: function(classes){
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
		success: function(detail){
			if (detail.pend){
				$(objDetail).html(detail.pend);
			}
		}
	});
}