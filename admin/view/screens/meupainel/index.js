var datasets = undefined;

$(document).ready(function(){
	$('[name=progress]')
		.on('click', function (e) {
			var oDet = $(this).find("#detalhes");
			showDetailClassReq( oDet, { id: $(this).attr("cad-id"), req: $(this).attr("req-id") } );
			oDet.show();
		});	
});