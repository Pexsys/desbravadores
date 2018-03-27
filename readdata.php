<?php
@require_once("include/functions.php");
session_start();
fHeaderPage( 
	array(
		$GLOBALS['pattern']->getVD()."include/_core/css/bootstrap-toggle.min.css",
		$GLOBALS['pattern']->getVD()."include/_core/css/bootstrap-touchspin.min.css"
	), 
	array( 
		$GLOBALS['pattern']->getVD()."include/_core/js/bootstrap-toggle.min.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/bootstrap-touchspin.min.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/moment.min.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/moment.pt-br.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/jstz.min.js",
		$GLOBALS['pattern']->getVD()."include/_core/js/jquery.mask.min.js",
		$GLOBALS['pattern']->getVD()."js/readdata.lib.js",
		$GLOBALS['pattern']->getVD()."js/readdata.js?".microtime()
	) 
);
?>
<body>
<?php
$temPerfil = isset($_SESSION['USER']['id']);
if (!$temPerfil):
	session_destroy();
	@include("include/login.php");
	?>
	<script type="text/javascript">
	$("#page").val("readdata");
	$("#myLoginModal").modal();
	</script>
	<?php
	exit;
endif;
?>
<div id="wrapper">
	<br/>
	<div id="page-wrapper">
		<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
			<input type="hidden" id="barfield"/>
			<form method="post" id="cadBarCode">
				<input type="hidden" id="tipoFuncao" field="op"/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-default" name="tpCapture" type-fn="CHAMADA" for="opChamad">
								<input type="radio" id="opChamad" autocomplete="off">Chamada
							</label>
							<label class="btn btn-default" name="tpCapture" type-fn="APRENDIZADO" for="opAprend">
								<input type="radio" id="opAprend" autocomplete="off">Aprendizado
							</label>
						</div>
					</div>
				</div>
				<div id="divDatas" style="display:none">
					<br/>
					<div class="well well-sm" style="padding:4px;margin-bottom:0px;font-size:11px"><b>Datas</b></div>
					<div class="row">
						<div class="form-group col-xs-6">
							<div class="input-group center-block">
								<label for="dtInicio">In&iacute;cio</label>
								<input type="checkbox" id="fgInicioNulo" name="toggle-dates" for="dtInicio" field="fg_inicio_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
								<input type="text" name="dtInicio" id="dtInicio" field="dt_inicio" class="form-control input-sm date" placeholder="In&iacute;cio"/>
							</div>
						</div>
						<div class="form-group col-xs-6">
							<div class="input-group center-block">
								<label for="dtConclusao">Conclus&atilde;o</label>
								<input type="checkbox" id="fgConclusaoNulo" name="toggle-dates" for="dtConclusao" field="fg_conclusao_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
								<input type="text" name="dtConclusao" id="dtConclusao" field="dt_conclusao" class="form-control input-sm date" placeholder="Conclus&atilde;o" style="display:none;"/>
							</div>
						</div>
						<div class="form-group col-xs-6">
							<div class="input-group center-block">
								<label for="dtAvaliacao">Avalia&ccedil;&atilde;o</label>
								<input type="checkbox" id="fgAvaliacaoNulo" name="toggle-dates" for="dtAvaliacao" field="fg_avaliacao_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
								<input type="text" name="dtAvaliacao" id="dtAvaliacao" field="dt_avaliacao" class="form-control input-sm date" placeholder="Avalia&ccedil;&atilde;o" style="display:none;"/>
							</div>
						</div>
						<div class="form-group col-xs-6">
							<div class="input-group center-block">
								<label for="dtInvestidura">Investidura</label>
								<input type="checkbox" id="fgInvestiduraNulo" name="toggle-dates" for="dtInvestidura" field="fg_investidura_alt" value-on="S" value-off="N" data-toggle="toggle" data-onstyle="danger" data-offstyle="warning" data-width="105" data-size="small" data-on="Alterar" data-off="N&atilde;o alterar"/>
								<input type="text" name="dtInvestidura" id="dtInvestidura" field="dt_investidura" class="form-control input-sm date" placeholder="Investidura" style="display:none;"/>
							</div>
						</div>
					</div>
				</div>
				<br/>
				<br/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
						<div class="input-group">
							<button type="button" class="btn btn-primary btn-lg" id="capture"><i class="glyphicon glyphicon-barcode"></i>&nbsp;Capturar</button>
						</div>
					</div>
				</div>
				<br/>
				<br/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
						<div class="panel panel-primary" id="divResultado" style="display:none">
							<div class="panel-heading" style="padding:3px 10px"><b>Resultado</b></div>
							<div class="panel-body" style="padding:5px 10px" id="strResultado"></div>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
						<div class="input-group">
							<button type="button" id="myBtnLogout" class="btn btn-info">Sair</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php @include_once("include/bottom_page.php");?>