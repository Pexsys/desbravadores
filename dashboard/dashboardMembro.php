<?php
$membroID = $_SESSION['USER']['id_cad_pessoa'];

function getClass( $pct ){
	if ($pct < 51):
		return "panel-danger";
	elseif ($pct < 85):
		return "panel-warning";
	elseif ($pct < 100):
		return "panel-primary";
	else:
		return "panel-green";
	endif;
}

function drawBoxesArea($title,$result,$boxClass = NULL){
	if (!$result->EOF):
		?>
		<div class="row">
			<div class="col-lg-12">
				<h4 class="page-header"><?php echo $title;?></h4>
			</div>
			<?php
			//TOTAL DE REQUISITOS POR CLASSE.
			foreach ($result as $k => $fields):
				$tp = $fields["TP_ITEM"];
				$value = ( $tp == "ES" ? $fields["CD_ITEM_INTERNO"] : "" );
				$icon = getIconAprendizado( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"], "fa-4x" );
				$area = getMacroArea( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"] );
				$class = (isset($boxClass) ? $boxClass : (empty($fields["BOX_CLASS"]) ? "panel-default" : $fields["BOX_CLASS"]));
				fItemAprendizado(array(
					"classPanel" => $class,
					"leftIcon" => $icon, 
					"value" => $value, 
					"title" => titleCase( $fields["DS_ITEM"] ), 
					"strBL" => titleCase( $area ), 
					"strBR" => strftime("%d/%m/%Y",strtotime($fields["DT"])), 
					"hint" => $fields["BOX_HINT"]
				));
			endforeach;
			?>
		</div>
		<?php
	endif;
}
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Meu Painel</h3>
	</div>
</div>

<?php
$result = $GLOBALS['conn']->Execute("
	SELECT ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO, COUNT(*) AS QT_REQ
	FROM CON_APR_PESSOA
	WHERE ID_CAD_PESSOA = ? AND DT_CONCLUSAO IS NULL
	GROUP BY ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO
	ORDER BY TP_ITEM, CD_ITEM_INTERNO
", array($membroID) );
if (!$result->EOF):
	?>
	<div class="row">
		<div class="col-lg-12">
			<h4 class="page-header">Itens em andamento em <?echo date("Y");?></h4>
		</div>
		<?php
		//TOTAL DE REQUISITOS POR CLASSE.
		foreach ($result as $k => $fields):
			$icon = getIconAprendizado( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"], "fa-4x" );
			$area = getMacroArea( $fields["TP_ITEM"], $fields["CD_AREA_INTERNO"] );
			
			if ($fields["TP_ITEM"] == "ES"):
				fItemAprendizado(array(
					"classPanel" => "panel-danger",
					"leftIcon" => $icon, 
					"value" => $fields["CD_ITEM_INTERNO"], 
					"title" => titleCase( $fields["DS_ITEM"] ), 
					"strBL" => titleCase( $area ), 
					"strBR" => strftime("%d/%m/%Y",strtotime($fields["DT_INICIO"]))
				));
			else:
				$qtdReq =  $fields["QT_REQ"];
				$tabAprID =  $fields["ID_TAB_APREND"];
				
				$pct = 0;
				$qtd = 0;
				$rs = $GLOBALS['conn']->Execute("
					SELECT COUNT(*) AS QT_COMPL
					FROM CON_APR_PESSOA
					WHERE ID_CAD_PESSOA = ?
					AND ID_TAB_APREND = ?
					AND DT_ASSINATURA IS NOT NULL
					AND DT_CONCLUSAO IS NULL
				", array($membroID, $tabAprID) );
				if (!$rs->EOF):
					$qtd = $rs->fields["QT_COMPL"];
				endif;
				
				$pct = floor( ( $qtd / $qtdReq ) * 100);
				fItemAprendizado(array(
					"classPanel" => getClass( $pct ),
					"leftIcon" => $icon, 
					"value" => "$pct%", 
					"title" => titleCase( $fields["DS_ITEM"] ), 
					"strBL" => titleCase( $area ), 
					"strBR" => "$qtd / $qtdReq", 
					"fields" => array( 
						"name" => "progress",
						"cad-id" => $fields["ID_CAD_PESSOA"],
						"req-id" => $fields["ID_TAB_APREND"]
					)
				));
			endif;
		endforeach;
		?>
	</div>
	<?php
endif;

$result = $GLOBALS['conn']->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_CONCLUSAO AS DT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND ah.DT_CONCLUSAO IS NOT NULL
	  AND ah.DT_AVALIACAO IS NULL
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($membroID) );
drawBoxesArea("Itens não avaliados pelo regional",$result,"panel-yellow");

$result = $GLOBALS['conn']->Execute("
   SELECT DISTINCT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_AVALIACAO AS DT, 
			 IF(cc.FG_COMPRA = 'S' OR ccag.ID IS NOT NULL,'panel-success','panel-warning') AS BOX_CLASS, 
			 IF(cc.FG_COMPRA = 'S' OR ccag.ID IS NOT NULL,'Item comprado','Item ainda não comprado') AS BOX_HINT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
 LEFT JOIN CON_COMPRAS cc ON (cc.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND cc.FG_COMPRA = 'S' AND cc.ID_TAB_APREND = ah.ID_TAB_APREND)
 LEFT JOIN CON_COMPRAS ccag ON (ccag.ID_CAD_PESSOA = ah.ID_CAD_PESSOA AND ccag.FG_COMPRA = 'S' AND ccag.ID_TAB_APREND = tm.ID_AGRUPADA)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND ah.DT_CONCLUSAO IS NOT NULL
	  AND ah.DT_AVALIACAO IS NOT NULL
	  AND ah.DT_INVESTIDURA IS NULL
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($membroID) );
drawBoxesArea("Itens a receber na próxima investidura",$result);

$result = $GLOBALS['conn']->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ta.CD_AREA_INTERNO, ah.DT_INVESTIDURA AS DT
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND YEAR(ah.DT_INVESTIDURA) = Year(NOW())
	ORDER BY ta.TP_ITEM, ta.CD_ITEM_INTERNO
", array($membroID) );
drawBoxesArea("Itens recebidos em ".date("Y"),$result,"panel-green");
?>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/aprendizadoFunctions.js<?php echo "?".microtime();?>"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/dashboardMembro.js<?php echo "?".microtime();?>"></script>