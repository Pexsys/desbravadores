<?php
function fHeaderDashboard(){
require_once("../include/_metaheader.php");
?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Pioneiros ADM v1.0</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Clube Pioneiros">
<meta name="author" content="Ricardo Jonadabs César">
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap-toggle.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap-touchspin.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/metisMenu.min.css" rel="stylesheet"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap-datetimepicker.min.css" rel="stylesheet"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/timeline.css" rel="stylesheet"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/sb-admin-2.css" rel="stylesheet"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/morris.css" rel="stylesheet"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/datatable/dataTables.bootstrap.min.css" rel="stylesheet"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/datatable/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/calendar.min.css" rel="stylesheet"/>
<style>
.badge-notify{
   background:#cc0000;
   position:relative;
   top:-8px;
   left:-8px;
}
</style>
</head>
<body>
<?php
}

function fFooterDashboardScript(){
?>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/jquery.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-dialog.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-toggle.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-touchspin.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-select.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-notify.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/calendar.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/calendar.lang.pt-BR.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/underscore-min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/moment.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/moment.pt-br.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/jstz.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/formValidation/formValidation.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/formValidation/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/jquery.mask.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/datatable/dataTables.tableTools.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/datatable/dataTables.buttons.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/datatable/ZeroClipboard.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/datatable/datetime-moment.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/datatable/plugins.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/metisMenu.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>js/functions.lib.js?<?php echo microtime();?>"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>js/notifications.lib.js?<?php echo microtime();?>"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/index.js?<?php echo microtime();?>"></script>
<?php
}
?>