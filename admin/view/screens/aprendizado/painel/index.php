<style>
path.slice{
	stroke-width:2px;
}
polyline{
	opacity: 1;
	stroke: black;
	stroke-width: 2px;
	fill: none;
}
svg text.percent{
	fill:white;
	text-anchor:middle;
	font-size:12px;
}
path { stroke: #fff; }
path:hover { opacity:0.5; }
rect { opacity:1; }
rect:hover { opacity:0.5; }
.legend tr{ border-bottom:1px solid grey;}
.legend tr:first-child { border-top:1px solid grey; }
.axis path,
.axis line {
  fill: none;
  stroke: #000;
}
.legend{
    margin-bottom:76px;
    display:inline-block;
    border-collapse: collapse;
    border-spacing: 0px;
}
.legend td{
    padding:4px 5px;
    vertical-align:middle;
}
.legendFreq {
    text-align:center;
}
.legendPerc{
    text-align:right;
}
</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Painel de Aprendizado do Clube em <?echo date("Y");?></h3>
	</div>
</div>
<div class="row" id="divphGhaph" style="display:none;">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
		<div id="phGhaph"></div>
	</div>
	<hr/>
</div>
<?php
$result = CONN::get()->Execute("
	SELECT COUNT(*) AS QTD
	FROM APR_HISTORICO h
	INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = h.ID_TAB_APREND)
	WHERE ta.TP_ITEM = 'ES'
	AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
");
if (!$result->EOF):
	$rs = CONN::get()->Execute("
		SELECT r.CD_ITEM_INTERNO, r.DS_ITEM, COUNT(*) AS QTD
		FROM APR_HISTORICO h
		INNER JOIN TAB_APRENDIZADO r ON (r.ID = h.ID_TAB_APREND)
		INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
		WHERE r.TP_ITEM = 'ES'
		AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
		GROUP BY r.CD_ITEM_INTERNO, r.DS_ITEM
		ORDER BY r.CD_ITEM_INTERNO
	");
	?>
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header"><?php echo $result->fields['QTD']?> Especialidades concluídas em <?echo date("Y");?><small> (<?php echo $rs->RecordCount();?> diferentes)</small></h3>
		</div>
	</div>
	<div class="row">
	<?php
	$result = CONN::get()->Execute("
		SELECT ra.CD_AREA_INTERNO, ra.DS_ITEM, COUNT(*) AS QTD
		FROM APR_HISTORICO h
		INNER JOIN TAB_APRENDIZADO r ON (r.ID = h.ID_TAB_APREND)
		INNER JOIN TAB_APRENDIZADO ra ON (ra.TP_ITEM = r.TP_item AND ra.CD_AREA_INTERNO = r.CD_AREA_INTERNO AND ra.CD_ITEM_INTERNO IS NULL)
		INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = h.ID_CAD_PESSOA)
		WHERE r.TP_ITEM = 'ES'
		  AND YEAR(h.DT_CONCLUSAO) = YEAR(NOW())
		GROUP BY ra.CD_AREA_INTERNO, ra.DS_ITEM
		ORDER BY ra.CD_AREA_INTERNO
	");

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
<script src="<?php echo PATTERNS::getVD();?>vendor/adminbsb/plugins/d3/d3.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>js/aprendizadoFunctions.js<?php echo "?".time();?>"></script>
<script src="<?php echo PATTERNS::getVD();?>admin/view/screens/aprendizado/painel/index.js<?php echo "?".time();?>"></script>
