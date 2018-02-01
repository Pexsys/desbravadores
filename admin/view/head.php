<head>
    <title><?php echo PATTERNS::getClubeDS(array("nm"));?> ADM v4.0</title>
    <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="<?php echo PATTERNS::getClubeDS(array("cl","nm"))?>">
    <meta name="author" content="Ricardo Jonadabs César">
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/favicon.ico" type="image/x-icon"rel="icon" />
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/material-icons/css/material-icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/node-waves/waves.min.css" rel="stylesheet" />
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/animate-css/animate.min.css" rel="stylesheet" />
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/morrisjs/morris.css" rel="stylesheet" />
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/dataTables.bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/multi-select/css/multi-select.css" rel="stylesheet">
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-spinner/css/bootstrap-spinner.min.css" rel="stylesheet">
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">

    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/css/style.min.css" rel="stylesheet">
    <link href="<?php echo PATTERNS::getVD();?>vendor/adminbsb/css/themes/all-themes.min.css" rel="stylesheet" />

    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/momentjs/moment.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/momentjs/moment.pt-br.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-spinner/js/jquery.spinner.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-steps/jquery.steps.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-sparkline/jquery.sparkline.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-countto/jquery.countTo.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/dataTables.tableTools.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/dataTables.buttons.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/ZeroClipboard.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/datetime-moment.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/jquery-datatable/plugins.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/node-waves/waves.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/waitme/waitMe.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/autosize/autosize.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/raphael/raphael.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/morrisjs/morris.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/multi-select/js/jquery.multi-select.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/chartjs/Chart.bundle.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/flot-charts/jquery.flot.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/flot-charts/jquery.flot.time.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/formValidation.io/formValidation.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/formValidation.io/bootstrap.min.js"></script>
    <script src="<?php echo PATTERNS::getVD();?>js/functions.lib.js<?php echo "?".time();?>"></script>
    <script>jsLIB.rootDir = '<?php echo PATTERNS::getVD();?>';</script>
    <script src="<?php echo PATTERNS::getVD();?>js/notifications.lib.js<?php echo "?".time();?>"></script>
</head>
