$(document).ready(function(){

	$("#btnGerar").click( function(e) {
		var opt = $(this).attr('report-id');
		var cmpl = $(this).attr('report-cmpl');
		if (opt){
			url = jsLIB.rootDir+'report/'+opt;
			if (cmpl){
				url += $("#"+cmpl).val();
			}
			window.open(url,'_blank','top=50,left=50,height=750,width=550,menubar=no,status=no,titlebar=no',true);
		}
	});

	$("#cbListagem").change(function(){
		rulesGeracao( $(this) );
	});
	
});

function rulesGeracao( obj ){
	$("[name=rowFilter]").visible(false);
	$("#btnGerar")
		.attr('report-id', obj.val() )
		.attr('report-cmpl', "" );
	
	var show = obj.find(":selected").attr('show'); 
	if (show !== undefined) {
		$("#"+show).visible(true);
		$("#btnGerar").attr('report-cmpl', $("#"+show).find(".selectpicker").attr("id") );
	}
	
	$("#btnGerar").visible( $("#cbListagem").val() !== '' );
}