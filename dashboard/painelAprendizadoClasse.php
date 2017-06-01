<?php
$membroID = $_SESSION['USER']['id_cad_pessoa'];
$classe = "";
$like = "";
$result = $GLOBALS['conn']->Execute("
	SELECT TP_SEXO, CD_CARGO, CD_CARGO2
	  FROM CON_ATIVOS 
	 WHERE ID = ?
", array($membroID) );
$cargo = $result->fields['CD_CARGO'];
if (fStrStartWith($cargo,"2-07")):
	$cargo = $result->fields['CD_CARGO2'];
endif;
if (empty($cargo)):
	exit;
endif;
if ($cargo <> "2-04-00" && fStrStartWith($cargo,"2-04")):
	$like = "01-".substr($cargo,-2);
endif;
$rc = $GLOBALS['conn']->Execute("
	SELECT DSF, DSM
      FROM TAB_CARGO
     WHERE CD = ?
", array($cargo) );
if (!$rc->EOF):
	$classe = $result->fields["TP_SEXO"] == "F" ? $rc->fields["DSF"] : $rc->fields["DSM"];
endif;
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Painel de Aprendizado <?echo date("Y");?> - <?echo titleCase( $classe );?></h3>
	</div>
</div>
<?php
$classGraphs = "col-md-12 col-sm-12 col-lg-12";
if (!empty($like)):
	$where .= " AND ta.CD_ITEM_INTERNO LIKE '$like%'";
	$classGraphs = "col-md-12 col-sm-12 col-lg-6";
endif;
$result = $GLOBALS['conn']->Execute("
	SELECT ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO, ta.CD_COR, ta.DS_ITEM, COUNT(*) as QTD
	  FROM APR_HISTORICO ah
	INNER JOIN CON_ATIVOS at ON (at.ID = ah.ID_CAD_PESSOA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	 WHERE ah.DT_CONCLUSAO IS NULL $where
	  GROUP BY ta.ID_TAB_APREND, ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO, ta.CD_COR, ta.DS_ITEM
");
if (!$result->EOF):
	?>
	<div class="row">
		<?php
		//ALUNOS DA CLASSE
		foreach ($result as $k => $line):
			$icon = getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"], "fa-4x" );
			$area = getMacroArea( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] );
			$color = (fStrStartWith($line["CD_ITEM_INTERNO"],"01-06") ? "#000000" : "#FFFFFF");
			fItemAprendizado( "panel-info", $icon, $line["QTD"], titleCase( $line["DS_ITEM"] ), titleCase( $area ), "Inscritos", "color:$color;background-color:".$line["CD_COR"] );
		endforeach;
		?>
	</div>
	<?php
endif;
?>
<div class="row">
	<div class="col-lg-12">
		<h4 class="page-header">Análises Gr&aacute;ficas da classe</h4>
	</div>
</div>
<div class="row">
	<div class="<?php echo $classGraphs;?>">
		<center><h5>An&aacute;lise gr&aacute;fica consolidada</h5></center>
		<div id="phGhaphC" style="width:100%;height:300px"></div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<h4 class="page-header">An&aacute;lise individual</h4>
	</div>
</div>
<?php
$result = $GLOBALS['conn']->Execute("
	SELECT DISTINCT at.NM, at.ID
	  FROM APR_HISTORICO ah
	INNER JOIN CON_ATIVOS at ON (at.ID = ah.ID_CAD_PESSOA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	 WHERE (ah.DT_INVESTIDURA IS NULL OR YEAR(ah.DT_INICIO) = YEAR(NOW())) $where
	  ORDER BY at.NM
");
if (!$result->EOF):
	echo "<div class=\"row\">";
	foreach ($result as $k => $line):
		$id = $line["ID"];
		echo "<div class=\"col-md-12 col-xs-12 col-sm-12 col-lg-6\">";
		echo "<div class=\"panel panel-info\" aria-expanded=\"false\" cad-id=\"$id\" itm-int-like=\"$like\">";
		echo "<div class=\"panel-heading\" style=\"cursor:pointer\">";
		echo "<h5 class=\"panel-title\" data-toggle=\"collapse\" href=\"#m$id\">";
		echo titleCase($line["NM"]);
		echo "<i class=\"pull-right glyphicon glyphicon-chevron-down\"></i>";
		echo "</h5>";
		echo "</div>";
		echo "<div id=\"m$id\" class=\"panel-body panel-collapse collapse\" style=\"padding-bottom:0px\"></div>";
		echo "</div>";
		echo "</div>";
	endforeach;
	echo "</div>";
endif;
?>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.axislabels.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.labels.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/aprendizadoFunctions.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/painelAprendizadoClasse.js<?php echo "?".microtime();?>"></script>