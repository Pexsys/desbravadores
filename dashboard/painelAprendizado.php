<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Painel de Aprendizado do Clube em <?echo date("Y");?></h3>
	</div>
</div>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
		<center><h4>An&aacute;lise gr&aacute;fica das classes em andamento</h4></center>
		<div id="phGhaphP" style="width:100%;height:300px"></div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-3">
		<center><h4>Requisitos das Classes Regulares</h4></center>
		<div id="phRegularP" style="width:100%;height:250px"></div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-3">
		<center><h4>Requisitos das Classes Avan&ccedil;adas</h4></center>
		<div id="phAvancadaP" style="width:100%;height:250px"></div>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
		<center><h4>An&aacute;lise gr&aacute;fica de classes conclu&iacute;das</h4></center>
		<div id="phGhaphC" style="width:100%;height:300px"></div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-3">
		<center><h4>Classes Regulares Conclu&iacute;das</h4></center>
		<div id="phRegularC" style="width:100%;height:250px"></div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-3">
		<center><h4>Classes Avan&ccedil;adas Conclu&iacute;das</h4></center>
		<div id="phAvancadaC" style="width:100%;height:250px"></div>
	</div>
</div>
<?php
$result = $GLOBALS['conn']->Execute("
	SELECT COUNT(*) AS QTD
	FROM APR_HISTORICO h
	INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = h.ID_TAB_APREND)
	WHERE ta.TP_ITEM = ?
	AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
", array("ES") );
if (!$result->EOF):
	$rs = $GLOBALS['conn']->Execute("
		SELECT r.CD_ITEM_INTERNO, r.DS_ITEM, COUNT(*) AS QTD
		FROM APR_HISTORICO h
		INNER JOIN TAB_APRENDIZADO r ON (r.ID = h.ID_TAB_APREND)
		INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
		WHERE r.TP_ITEM = ?
		AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
		GROUP BY r.CD_ITEM_INTERNO, r.DS_ITEM
		ORDER BY r.CD_ITEM_INTERNO	
	", array("ES") );
	?>
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header"><?php echo $result->fields['QTD']?> Especialidades concluídas em <?echo date("Y");?><small> (<?php echo $rs->RecordCount();?> diferentes)</small></h3>
		</div>
	</div>
	<div class="row">
	<?php
	$result = $GLOBALS['conn']->Execute("
		SELECT ra.CD_AREA_INTERNO, ra.DS_ITEM, COUNT(*) AS QTD
		FROM APR_HISTORICO h
		INNER JOIN TAB_APRENDIZADO r ON (r.ID = h.ID_TAB_APREND)
		INNER JOIN TAB_APRENDIZADO ra ON (ra.TP_ITEM = r.TP_item AND ra.CD_AREA_INTERNO = r.CD_AREA_INTERNO AND ra.CD_ITEM_INTERNO IS NULL)
		INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
		WHERE r.TP_ITEM = ?
		  AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
		GROUP BY ra.CD_AREA_INTERNO, ra.DS_ITEM
		ORDER BY ra.CD_AREA_INTERNO
	", array("ES") );

	foreach ($result as $k => $line):
		$cdArea = $line["CD_AREA_INTERNO"];
		echo "<div class=\"col-sm-12 col-xs-12 col-md-6 col-lg-6\">";
		echo "<div class=\"panel panel-info\" aria-expanded=\"false\" cd-area=\"$cdArea\">";
		echo "<div class=\"panel-heading\" style=\"cursor:pointer\">";
		echo "<h5 class=\"panel-title\" data-toggle=\"collapse\" data-target=\"#$cdArea\">";
		echo ($line["DS_ITEM"])."<span class=\"badge pull-right\">".$line["QTD"]."</span>";
		echo "</h5></div>";
		echo "<div id=\"$cdArea\" class=\"panel-body panel-collapse collapse\"></div></div></div>";
	endforeach;
	?>			
	</div>
	<?php
endif;
?>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/flot/jquery.flot.min.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/flot/jquery.flot.pie.min.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/flot/jquery.flot.axislabels.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/flot/jquery.flot.labels.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/flot/jquery.flot.orderBars.js"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/aprendizadoFunctions.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo $GLOBALS['pattern']->getVD();?>dashboard/js/painelAprendizado.js<?php echo "?".microtime();?>"></script>