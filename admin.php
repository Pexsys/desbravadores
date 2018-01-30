<?php
@require_once("include/functions.php");
session_start();
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html">
<title><?php echo PATTERNS::getClubeDS(array("cl","nm","sp","ibd"));?></title>
<?php @require_once("include/_metaheader.php");?>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-dialog.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/moment.pt-br.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/jstz.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/jquery.mask.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/formValidation/formValidation.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>include/_core/js/formValidation/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>js/functions.lib.js"></script>
<script type="text/javascript" src="<?php echo PATTERNS::getVD();?>js/readdata.js<?php echo "?".microtime();?>"></script>
</head>
<body>
<?php
$temPerfil = isset($_SESSION['USER']['perfil']);
if (!$temPerfil):
	session_destroy();
	@include("include/login.php");
	?>
	<script type="text/javascript">
		$("#myLoginModal").modal();
	</script>
	<?php
	exit;
endif;
?>
<?php @require_once("include/bottom_page.php");?>
