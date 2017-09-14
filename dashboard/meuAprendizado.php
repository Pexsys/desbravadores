<?php
function getClassPainelMestrado( $pct ){
	if ($pct < 50):
		return "panel-default";
	elseif ($pct < 75):
		return "panel-yellow";
	elseif ($pct < 100):
		return "panel-red";
	else:
		return "panel-green";
	endif;
}

$membroID = $_SESSION['USER']['id_cad_pessoa'];
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">Meu aprendizado</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<h4 class="page-header">An&aacute;lise gr&aacute;fica de <?echo date("Y");?></h4>
		<div id="content">
			<div id="placeholder" style="width:100%;height:400px"></div>
			<div id="choices" style="width:100%"></div>
		</div>
	</div>
</div>
<hr/>
<div class="row">
<?php
$result = $GLOBALS['conn']->Execute("
	SELECT TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO, MAX(DT_ASSINATURA) AS DT_ASSINATURA
	FROM CON_APR_PESSOA
	WHERE ID_CAD_PESSOA = ?
	  AND YEAR(DT_INICIO) = YEAR(NOW())
	  AND DT_CONCLUSAO IS NULL
	GROUP BY TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO
	ORDER BY DT_INICIO, TP_ITEM, CD_ITEM_INTERNO
", array($membroID) );
if (!$result->EOF):
?>
	<div class="col-lg-6">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h4>Itens atuais pendentes</h4>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Item</th>
								<th>In&iacute;cio</th>
								<th>&Uacute;ltima Assinatura</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $key => $line ):
								echo "<tr>
									<td><i class=\"".getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($line["DS_ITEM"])."</td>
									<td>".date( 'd/m/Y', strtotime($line["DT_INICIO"]) )."</td>
									<td>". (!is_null($line["DT_ASSINATURA"]) ? date( 'd/m/Y', strtotime($line["DT_ASSINATURA"]) ) : "")."</td>
								</tr>";
							endforeach;
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php
endif;

$result = $GLOBALS['conn']->Execute("
	SELECT TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO, MAX(DT_ASSINATURA) AS DT_ASSINATURA
	FROM CON_APR_PESSOA
	WHERE ID_CAD_PESSOA = ?
	  AND YEAR(DT_INICIO) < YEAR(NOW())
	  AND DT_CONCLUSAO IS NULL
	GROUP BY TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO
	ORDER BY DT_INICIO, TP_ITEM, CD_ITEM_INTERNO
", array($membroID) );
if (!$result->EOF):
?>
	<div class="col-lg-6">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<h4>Itens anteriores pendentes</h4>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Item</th>
								<th>In&iacute;cio</th>
								<th>&Uacute;ltima Assinatura</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $key => $line ):
								echo "<tr>
									<td><i class=\"".getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($line["DS_ITEM"])."</td>
									<td>".date( 'd/m/Y', strtotime($line["DT_INICIO"]) )."</td>
									<td>". (!is_null($line["DT_ASSINATURA"]) ? date( 'd/m/Y', strtotime($line["DT_ASSINATURA"]) ) : "")."</td>
								</tr>";
							endforeach;
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php
endif;
?>
<div class="row">
	<div class="col-lg-12">
		<h4 class="page-header">Painel de Mestrados</h4>
	</div>
	<?php
	
	//LE REGRAS
	$rg = $GLOBALS['conn']->Execute("
	    SELECT DISTINCT car.ID, car.CD_ITEM_INTERNO, car.CD_AREA_INTERNO, car.DS_ITEM, car.TP_ITEM, car.MIN_AREA
	      FROM CON_APR_REQ car
	     WHERE car.CD_AREA_INTERNO = ?
	  ORDER BY car.CD_ITEM_INTERNO
	", array("ME") );
	foreach ($rg as $lg => $fg):
        $min = $fg["MIN_AREA"];
        $feitas = 0;
        
        //LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
	    $rR = $GLOBALS['conn']->Execute("
            SELECT tar.QT_MIN, COUNT(*) AS QT_FEITAS
            FROM TAB_APR_REQ tar
            INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_REQ = tar.ID AND car.TP_ITEM_RQ = ?)
            INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
            WHERE tar.ID_TAB_APREND = ?
	    ", array( "ES", $membroID, $fg["ID"] ) );
	    foreach($rR as $lR => $fR):
            $feitas += min( $fR["QT_MIN"], $fR["QT_FEITAS"] );
        endforeach;
	    
	    $icon = getIconAprendizado( $fg["TP_ITEM"], $fg["CD_AREA_INTERNO"], "fa-4x" );
		$area = getMacroArea( $fg["TP_ITEM"], $fg["CD_AREA_INTERNO"] );
		$pct = floor( ( $feitas / $min ) * 100);
	    fItemAprendizado( getClassPainelMestrado( $pct ), $icon, $fg["CD_ITEM_INTERNO"], titleCase( substr($fg["DS_ITEM"],12) ), titleCase( $area ), "$feitas / $min", null, null, "col-md-4 col-xs-8 col-sm-6 col-lg-3" );
	endforeach;
	?>
</div>


<?php
$result = $GLOBALS['conn']->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.DS_ITEM, ah.DT_INICIO, ah.DT_CONCLUSAO, ah.DT_AVALIACAO, ah.DT_INVESTIDURA
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND ah.DT_INVESTIDURA IS NOT NULL 
	  AND YEAR(ah.DT_INVESTIDURA) < YEAR(NOW())
	ORDER BY ah.DT_CONCLUSAO DESC, ta.TP_ITEM, ta.CD_ITEM_INTERNO DESC
", array($membroID) );
if (!$result->EOF):
?>
<div class="row">
	<div class="col-lg-12">
		<h4 class="page-header">Itens anteriores j&aacute; recebidos</h4>
	</div>
	<div class="col-lg-12">
		<div class="panel panel-success">
			<div class="panel-body" style="height:300px;overflow-y:scroll;">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Item</th>
								<th>In&iacute;cio</th>
								<th>Conclus&atilde;o</th>
								<th>Avalia&ccedil;&atilde;o</th>
								<th>Investidura</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($result as $key => $line ):
								echo "<tr>
									<td><i class=\"".getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($line["DS_ITEM"])."</td>
									<td>".date( 'd/m/Y', strtotime($line["DT_INICIO"]) )."</td>
									<td>".date( 'd/m/Y', strtotime($line["DT_CONCLUSAO"]) )."</td>
									<td>".date( 'd/m/Y', strtotime($line["DT_AVALIACAO"]) )."</td>
									<td>".date( 'd/m/Y', strtotime($line["DT_INVESTIDURA"]) )."</td>
								</tr>";
							endforeach;
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php
endif;
?>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.time.min.js"></script>    
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.symbol.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/flot/jquery.flot.dashes.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>dashboard/js/meuAprendizado.js<?php echo "?".microtime();?>"></script>