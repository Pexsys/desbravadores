<?php
@require_once("../include/filters.php");
$cadMembroID = $_SESSION['USER']['id_cad_membro'];
$unidade = "";
$result = CONN::get()->Execute("
	SELECT TP_SEXO, CD_CARGO, ID_UNIDADE, DS_UNIDADE
	  FROM CON_ATIVOS 
	 WHERE NR_ANO = YEAR(NOW()) 
	   AND ID_CAD_MEMBRO = ?
", array($cadMembroID) );
$unidadeID = $result->fields['ID_UNIDADE'];
if (empty($unidadeID)):
	exit;
endif;

$where = "ca.ID_UNIDADE = $unidadeID";
$classGraphs = "col-md-12 col-sm-12 col-lg-6";

$cargo = $result->fields['CD_CARGO'];
if (!fStrStartWith($cargo,"2-07")):
	$where .= " OR ca.CD_CARGO LIKE '2-07%'";	
	$classGraphs = "col-md-12 col-sm-12 col-lg-12";
endif;
$rc = CONN::get()->Execute("
    SELECT DSF, DSM
      FROM TAB_CARGO
     WHERE CD = ?
", array($cargo) );
if (!$rc->EOF):
	$unidade = ($result->fields["TP_SEXO"] == "F" ? $rc->fields["DSF"] : $rc->fields["DSM"]) ." - UNIDADE ". $result->fields['DS_UNIDADE'];
endif;
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Painel da Unidade - <?echo titleCase( $unidade );?></h3>
	</div>
</div>
<div class="row">
  <div class="col-lg-12">
	<?php fDataFilters( 
		array( 
			"filterTo" => "#members",
			"filters" => 
				array( 
          array( "id" => "B", "ds" => "Batizado", "icon" => "fas fa-bath" ),
          array( "id" => "C", "ds" => "Classe", "icon" => "fas fa-graduation-cap" ),
          array( "id" => "MA", "ds" => "Mês de Aniversário", "icon" => "far fa-calendar-alt" ),
					array( "id" => "RG", "ds" => "Regime Alimentar", "icon" => "fas fa-utensils" ),
          array( "id" => "RE", "ds" => "Restrição Alimentar", "icon" => "fas fa-utensils" ),
				)
		) 
	);?>
	</div>
	<div class="col-lg-12">
		<table class="compact row-border hover stripe display" style="cursor: pointer;" cellspacing="0" width="100%" id="members">
			<thead>
				<tr>
					<th></th>
					<th>Nome Completo</th>
          <th>Cargo</th>
					<th>Contato</th>
					<th>Aniversário</th>
					<th>Idade</th>
				</tr>
			</thead>
		</table>
		<br/>
	</div>
</div>
<?php
$result = CONN::get()->Execute("
	SELECT ta.TP_ITEM, ta.CD_AREA_INTERNO, COUNT(*) as QTD
	  FROM APR_HISTORICO ah
	INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	 WHERE ah.DT_CONCLUSAO IS NULL AND ta.TP_ITEM = 'CL'". (!empty($where)?" AND ($where)":"")." 
	  GROUP BY ta.CD_AREA_INTERNO DESC
");
if (!$result->EOF):
	echo "
	<div class=\"row\">
		<div class=\"col-lg-12\">
			<h4 class=\"page-header\">Painel de Aprendizado Geral - ".date("Y")."</h4>
		</div>
	</div>
	<div class=\"row\">";
	foreach ($result as $k => $line):
		$icon = getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"], "fa-4x" );
		$area = getMacroArea( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] );
		echo fItemAprendizado(array(
			"classPanel" => "panel-primary",
			"leftIcon" => $icon, 
			"value" => $line["QTD"], 
			"strBL" => titleCase( $area ), 
			"strBR" => "Inscritos"
		));
	endforeach;
	echo "</div>";
endif;
$result = CONN::get()->Execute("
	SELECT ta.ID, ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO, ta.CD_COR, ta.DS_ITEM, COUNT(*) as QTD
	  FROM APR_HISTORICO ah
	INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	 WHERE ah.DT_CONCLUSAO IS NULL". (!empty($where)?" AND ($where)":"")." 
	  GROUP BY ta.ID, ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO, ta.CD_COR, ta.DS_ITEM
");
if (!$result->EOF):
	echo "
	<div class=\"row\">
		<div class=\"col-lg-12\">
			<h4 class=\"page-header\">Painel de Aprendizado por Classe - ".date("Y")."</h4>
		</div>
	</div>
	<div class=\"row\">";
	//ALUNOS DA CLASSE
	foreach ($result as $k => $line):
		$icon = getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"], "fa-4x" );
		$area = getMacroArea( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] );

		$style = null;
		if ($line["TP_ITEM"] == "CL"):
			$back = $line["CD_COR"];
			$color = (fStrStartWith($line["CD_ITEM_INTERNO"],"01-06") ? "#000000" : "#FFFFFF");
			$style = "color:$color;background-color:$back";
		endif;

		echo fItemAprendizado(array(
			"classPanel" => "panel-info",
			"leftIcon" => $icon, 
			"value" => $line["QTD"], 
			"title" => titleCase( $line["DS_ITEM"] ), 
			"strBL" => titleCase( $area ), 
			"strBR" => "Inscritos", 
			"style" => $style
		));
	endforeach;
	echo "</div>";
endif;
?>
<div class="row" name="graphicC" style="display:none">
	<div class="col-lg-12">
		<h4 class="page-header">Análises Gr&aacute;ficas da unidade</h4>
	</div>
</div>
<div class="row" name="graphicC" style="display:none">
	<div class="<?php echo $classGraphs;?>">
		<center><h5>An&aacute;lise gr&aacute;fica consolidada</h5></center>
		<div id="phGhaphC" style="width:100%;height:300px"></div>
	</div>
</div>
<?php
$result = CONN::get()->Execute("
	SELECT DISTINCT ca.NM, ca.ID_CAD_PESSOA, ca.ID_MEMBRO
	  FROM APR_HISTORICO ah
	INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_PESSOA = ah.ID_CAD_PESSOA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	  WHERE (ah.DT_INVESTIDURA IS NULL OR YEAR(ah.DT_INICIO) = YEAR(NOW()))". (!empty($where)?" AND ($where)":"")." 
	  ORDER BY ca.NM
");
if (!$result->EOF):
	echo "
	<div class=\"row\">
		<div class=\"col-lg-12\">
			<h4 class=\"page-header\">An&aacute;lise individual</h4>
		</div>
	</div>
	<div class=\"row\">";

	//ALUNOS DA CLASSE
	foreach ($result as $k => $line):
		$id = $line["ID_CAD_PESSOA"];

		$barCODE = PATTERNS::getBars()->encode(array(
			"ni" => $line["ID_MEMBRO"]
		));

		echo "<div class=\"col-md-12 col-xs-12 col-sm-12 col-lg-6\">";
		echo "<div class=\"panel panel-info\" aria-expanded=\"false\" cad-id=\"$id\" unidade=\"$unidadeID\">";
		echo "<div class=\"panel-heading\" style=\"cursor:pointer\">";
		echo "<h5 class=\"panel-title\" data-toggle=\"collapse\" href=\"#m$id\">";
		echo "&nbsp;<i class=\"pull-left glyphicon glyphicon-chevron-down\"></i>";
		echo titleCase($line["NM"]);
		echo "<small class=\"pull-right\">$barCODE</small>";
		echo "</h5>";
		echo "</div>";
		echo "<div id=\"m$id\" class=\"panel-body panel-collapse collapse\" style=\"padding-bottom:0px\"></div>";
		echo "</div>";
		echo "</div>";
	endforeach;
	echo "</div>";
endif;
?>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/flot/jquery.flot.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/flot/jquery.flot.axislabels.js"></script>
<script src="<?php echo PATTERNS::getVD();?>include/_core/js/flot/jquery.flot.labels.js"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/aprendizadoFunctions.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo PATTERNS::getVD();?>dashboard/js/painelAprendizadoUnidade.js<?php echo "?".microtime();?>"></script>
