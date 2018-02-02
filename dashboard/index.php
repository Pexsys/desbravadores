<?php
session_start();
@require_once("../include/functions.php");
@require_once("include/control.php");

PROFILE::verificaPerfil();

fHeaderDashboard();
?>
<div id="wrapper">
	<!-- Navigation -->
	<?php @include_once("include/topbar.php");?>
	<?php fFooterDashboardScript();?>
	<div id="page-wrapper">
	<?php @include_once($activeOpt["url"]);?>
	</div>
</div>
<script language="javascript">
	$(document).ready(function(){
		$("#myBtnLogout").click(function(){
			jsLIB.ajaxCall({
				waiting : true,
				type: "GET",
				url: jsLIB.rootDir+'rules/login.php',
				data: { MethodName : 'logout' },
				success: function( data, jqxhr ) {
					window.location.replace( jsLIB.rootDir+'index.php')
				}
			});
		});
	});	
</script>
<?php @include_once("../include/bottom_page.php");?>