<?php
$pessoaID = $_SESSION['USER']['ID_CAD_PESSOA'];
?>
<style>
.blink{
    animation:blink 600ms infinite alternate;
}
@keyframes blink {
    from { opacity:1; }
    to { opacity:0.5; }
};
</style>
<div class="row">
	<div class="col-lg-12">
		<a id="mestrados"></a><h3 class="page-header">Meu aprendizado</h3>
	</div>
</div>
<div id="divGraph" style="display:none">
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
</div>
<div class="row">
<?php
$result = CONN::get()->Execute("
	SELECT TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO, MAX(DT_ASSINATURA) AS DT_ASSINATURA
	FROM CON_APR_PESSOA
	WHERE ID_CAD_PESSOA = ?
	  AND YEAR(DT_INICIO) = YEAR(NOW())
	  AND DT_CONCLUSAO IS NULL
	GROUP BY TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO
	ORDER BY DT_INICIO, TP_ITEM, CD_ITEM_INTERNO
", array($pessoaID) );
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

$result = CONN::get()->Execute("
	SELECT TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO, MAX(DT_ASSINATURA) AS DT_ASSINATURA
	FROM CON_APR_PESSOA
	WHERE ID_CAD_PESSOA = ?
	  AND YEAR(DT_INICIO) < YEAR(NOW())
	  AND DT_CONCLUSAO IS NULL
	GROUP BY TP_ITEM, DS_ITEM, CD_AREA_INTERNO, DT_INICIO
	ORDER BY DT_INICIO, TP_ITEM, CD_ITEM_INTERNO
", array($pessoaID) );
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
<div class="row" id="painelMestrados">
	<div class="col-lg-12">
		<h4 class="page-header">Painel de Mestrados</h4>
	</div>
</div>

<?php
$result = CONN::get()->Execute("
   SELECT ta.TP_ITEM, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO, ta.DS_ITEM, ah.DT_INICIO, ah.DT_CONCLUSAO, ah.DT_AVALIACAO, ah.DT_INVESTIDURA
	 FROM APR_HISTORICO ah
INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.ID_CAD_PESSOA = ?
	  AND ah.DT_INVESTIDURA IS NOT NULL
	  AND YEAR(ah.DT_INVESTIDURA) < YEAR(NOW())
	ORDER BY ah.DT_CONCLUSAO DESC, ta.TP_ITEM, ta.CD_ITEM_INTERNO DESC
", array($pessoaID) );
if (!$result->EOF):
?>
<div class="row">
	<div class="col-lg-12">
		<h4 class="page-header">Itens anteriores j&aacute; recebidos</h4>
	</div>
	<div class="col-lg-12">
		<div class="panel panel-success">
			<div class="panel-body" style="height:450px;overflow-y:scroll;">
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
							$total = $result->RecordCount();
							$size = strlen($total);
							foreach ($result as $key => $line):
								echo "<tr>
									<td>".fStrZero($total--,$size)." - <i class=\"".getIconAprendizado( $line["TP_ITEM"], $line["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($line["DS_ITEM"])."</td>
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
<script src="<?php echo PATTERNS::getVD();?>admin/view/screens/aprendizado/meu/index.js<?php echo "?".time();?>"></script>
