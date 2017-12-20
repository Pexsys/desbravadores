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

function showDetailClass( objThis, parameters ){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/painelAprendizado.php",
		data: { MethodName : 'getClasses', data : parameters },
		success: function(classes){
			if (classes.detail){
				var cadID = $(objThis).attr("cad-id");
				var objDetail = $(objThis).find("#m"+cadID);
				$(objDetail).html(classes.detail);

				$(objThis).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
				
				$('[name=progress]')
					.on('click', function (e) {
						showDetailClassReq( $(this).find("#detalhes"), { id: cadID, req: $(this).attr("req-id") } );
					});
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