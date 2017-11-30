function showDetailEspec( objDetail, parameters ){
	jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getEspec', data : parameters }, function(espec){
		if (espec.detail){
			$(objDetail).html(espec.detail);
		}
	});
}

function showDetailEspecPeople( objDetail, parameters ){
	jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getEspecPeople', data : parameters }, function(espec){
		if (espec.people){
			$(objDetail).html(espec.people);
		}
	});
}

function showDetailClass( objDetail, parameters ){
	jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getClasses', data : parameters }, function(classes){
		if (classes.detail){
			$(objDetail).html(classes.detail);
		}
	});
}

function showDetailClassReq( objDetail, parameters ){
	jsLIB.ajaxCall( undefined, jsLIB.rootDir+"rules/painelAprendizado.php", { MethodName : 'getPendentes', data : parameters }, function(detail){
		if (detail.pend){
			$(objDetail).html(detail.pend);
		}
	});
}