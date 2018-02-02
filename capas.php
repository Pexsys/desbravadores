<?php 
@require_once("include/functions.php");


fHeaderPage(
	array(  
		$GLOBALS['pattern']->getVD()."include/_core/css/datatable/dataTables.bootstrap.min.css",
		$GLOBALS['pattern']->getVD()."include/_core/css/datatable/jquery.dataTables.min.css"
	),
	array( 
		$GLOBALS['pattern']->getVD()."include/_core/js/slimscroll/jquery.slimscroll.min.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/datatable/jquery.dataTables.min.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/datatable/ZeroClipboard.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/datatable/dataTables.tableTools.min.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/datatable/dataTables.bootstrap.min.js",
		$GLOBALS['pattern']->getVD()."js/capas.js?".microtime()
	)
);
?>
<body>
    <!-- Navigation -->
	<?php @include_once("include/navbar.php");?>
	
    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1 class="page-header">Gerar Capas de Especialidades</h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">In&iacute;cio</a></li>
                    <li class="active">Gerar Capas</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->
        
		<div class="panel panel-default">
			<div class="container-fluid">
				<br/>
				<style type="text/css">
					#capas-form .inputGroupContainer .form-control-feedback,
					#capas-form .selectContainer .form-control-feedback {
						top: -3px;
					}
				</style>
				<form class="form-horizontal" method="post" id="capas-form">
					<div class="col-xs-12 col-md-12">
						<div class="form-group form-group-sm">
							<label class="col-xs-1 control-label" for="cdMembro">C&oacute;digo</label>
							<div class="col-xs-4 inputGroupContainer">
								<div class="input-group">
									<div class="input-group-addon"><i class="fas fa-barcode form-group-sm"></i></div>
									<input type="text" id="cdMembro" name="cdMembro" field="cd_membro" class="form-control" placeholder="Pasta/Caderno/Cart&atilde;o" style="text-transform: uppercase"
										maxlength="<?php echo $GLOBALS['pattern']->getBars()->getLength();?>" 
										pattern="<?php echo $GLOBALS['pattern']->getBars()->getPattern(".");?>"
										data-fv-regexp-message="C&oacute;digo inv&aacute;lido"
									/>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-12">
						<div class="form-group form-group-sm">
							<label class="col-xs-1 control-label" for="nmMembro">Nome</label>
							<div class="col-xs-9 inputGroupContainer">
								<div class="input-group">
									<div class="input-group-addon"><i class="far fa-user form-group-sm"></i></div>
									<input type="hidden" id="id" name="id" field="id" class="form-control"/>
									<input type="text" id="nmMembro" name="nmMembro" field="nm_membro" class="form-control" placeholder="Nome Completo"/>
								</div>
							</div>
						</div>
					</div>
					<hr/>
					<div class="col-xs-12 col-md-12">
						<div class="row form-group" style="margin-bottom:20px">
							<table class="compact row-border hover stripe" style="cursor:pointer" cellspacing="0" width="100%" id="simpledatatable">
								<thead>
									<tr>
										<th>C&oacute;d.</th>
										<th>Especialidade</th>
										<th>&Aacute;rea</th>
									</tr>
								</thead>
							<tbody/>
							</table>
						</div>
					</div>
					<div class="col-xs-12 col-md-12">
						<div class="row form-group form-group-sm">
							<input type="button" id="clearSelection" class="btn btn-warning  pull-left" value="Limpar Selecionadas"/>
							<input type="submit" class="btn btn-success pull-right" value="Gerar Selecionadas"/>
						</div>
					</div>
				</form>
				<br/>
			</div>
		</div>
	
    <!-- Footer -->
	<?php @include_once("include/footer.php");?>

    </div>
    <!-- /.container -->

<?php @include_once("include/bottom_page.php");?>