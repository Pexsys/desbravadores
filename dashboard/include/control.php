<?php
function fHeaderDashboard(){
require_once("../include/_metaheader.php");
?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title><?php echo PATTERNS::getClubeDS(array("nm"));?> ADM v3.0</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo PATTERNS::getClubeDS(array("cl","nm"))?>">
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap-toggle.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap-touchspin.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/font-awesome/css/fontawesome-all.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/metisMenu.min.css" rel="stylesheet"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/bootstrap-datetimepicker.min.css" rel="stylesheet"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/timeline.css" rel="stylesheet"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/sb-admin-2.css" rel="stylesheet"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/morris.css" rel="stylesheet"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/datatable/dataTables.bootstrap.min.css" rel="stylesheet"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/datatable/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="<?php echo PATTERNS::getVD();?>include/_core/css/calendar.min.css" rel="stylesheet"/>
<style>
.badge-notify{
   background:#cc0000;
   position:relative;
   top:-8px;
   left:-8px;
}
.dropdown-menu>li>a.minhaCapa {
  background: #ffff00 !important;
  font-weight: bolder;
}
.typeahead { z-index: 1051; }
</style>
</head>
<body>
<?php
}

function fFooterDashboardScript(){
?>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/jquery.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-dialog.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-toggle.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-touchspin.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-select.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-notify.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap3-typeahead.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/calendar.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/calendar.lang.pt-BR.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/underscore-min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/moment.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/moment.pt-br.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/jstz.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/formValidation/formValidation.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/formValidation/bootstrap.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/jquery.mask.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/datatable/dataTables.tableTools.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/datatable/dataTables.buttons.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/datatable/ZeroClipboard.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/datatable/datetime-moment.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/datatable/plugins.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/metisMenu.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>js/functions.lib.js?<?php echo microtime();?>"></script>
<script>jsLIB.rootDir = '<?php echo PATTERNS::getVD();?>';</script>
<script src="<?php echo PATTERNS::getVD();?>js/notifications.lib.js?<?php echo microtime();?>"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/index.js?<?php echo microtime();?>"></script>
<?php
}
?>