<?php
@require_once("include/functions.php");
fHeaderPage(
	array(
		PATTERNS::getVD()."vendor/adminbsb/plugins/jquery-datatable/dataTables.bootstrap.min.css"
	),
	array(
		PATTERNS::getVD()."vendor/adminbsb/plugins/jquery-datatable/jquery.dataTables.js",
		PATTERNS::getVD()."vendor/adminbsb/plugins/jquery-datatable/dataTables.bootstrap.min.js",
		PATTERNS::getVD()."js/capas.js?".time()
	)
);
?>
<body>
	<?php @require_once("include/navbar.php");?>
    <div class="container" style="margin:60px">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1 class="page-header">Gerar Capas de Especialidades</h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">In&iacute;cio</a></li>
                    <li class="active">Gerar Capas</li>
                </ol>
            </div>
        </diV>
		<br/>
		<div class="card">
			<div class="body">
				<form method="post" id="capas-form" autocomplete="off">
					<input type="hidden" id="id" name="id" field="id" class="form-control"/>
					<div class="row clearfix">
						<div class="col-xs-7 col-md-3 col-lg-3">
							<div class="form-group form-float form-group-lg">
								<div class="form-line">
									<input type="text" id="cdMembro" name="cdMembro" field="cd_membro" class="form-control" style="text-transform: uppercase"
										maxlength="<?php echo PATTERNS::getBars()->getLength();?>"
										pattern="<?php echo PATTERNS::getBars()->getPattern(".");?>"
										data-fv-regexp-message="C&oacute;digo inv&aacute;lido"
									/>
									<label class="form-label" for="cdMembro"><i class="fa fa-barcode"></i>&nbsp;C&oacute;digo de barras</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-md-12 col-lg-12">
							<div class="form-group form-float form-group-lg">
								<div class="form-line">
									<input type="text" id="nmMembro" name="nmMembro" field="nm_membro" class="form-control"/>
									<label class="form-label" for="nmMembro"><i class="fa fa-user"></i>&nbsp;Nome Completo</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="row form-group" style="margin-bottom:20px">
							<table class="table table-condensed table-hover compact" style="cursor:pointer" cellspacing="0" width="100%" id="simpledatatable">
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
			</div>
		<div>
		<br/>
		<?php @require_once("include/footer.php");?>
    </div>
<?php @require_once("include/bottom_page.php");?>
