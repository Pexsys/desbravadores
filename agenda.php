<?php
@require_once("include/functions.php");
fHeaderPage( array( PATTERNS::getVD()."css/index.css?" )
		   , array( PATTERNS::getVD()."js/index.js?") );
?><body>

    <!-- Navigation -->
	<?php @require_once("include/navbar.php");?>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Nossos Eventos
                    <small>Fique ligado</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">In&iacute;cio</a>
                    </li>
                    <li class="active">Nossos Eventos</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

	<?php
	$DATA_NOW = date('Y-m-d H:i:s');

	$query = "SELECT e.*, c.tp_grupo, t.ds
		    FROM CAD_EVENTOS e
	       LEFT JOIN RGR_CHAMADA c ON (c.ID_EVENTO = e.ID_EVENTO)
	       LEFT JOIN TAB_TP_UNIFORME t ON (t.ID = c.ID_TAB_TP_UNIFORME)
		   WHERE e.FLAG_PUBLICACAO = 'S' AND
			( e.DTHORA_EVENTO_INI >= ? OR ( e.DTHORA_EVENTO_FIM IS NOT NULL AND e.DTHORA_EVENTO_FIM >= ? ) )
		ORDER BY e.DTHORA_EVENTO_INI ";
	$result = CONN::get()->Execute( $query, Array( $DATA_NOW, $DATA_NOW ) );

	$MES_ANT = "";
	while (!$result->EOF):
		$MES_ATU = utf8_encode(ucfirst(strftime("%B", strtotime($result->fields['DTHORA_EVENTO_INI']))));

		if ($MES_ATU != $MES_ANT):
			$MES_ANT = $MES_ATU;
			?>
			<div class="row col-lg-12">
				<h2 class="page-header"><b><?php echo $MES_ANT;?></b></h2>
			</div>
			<?php
		endif;
		?>
		<div class="row media col-lg-12">
			<div class="pull-left">
				<span class="fa-stack fa-2x">
					  <i class="fa fa-circle fa-stack-2x text-primary"></i>
					  <i class="fa <?php echo fGetClassTipoEvento($result->fields['TIPO_EVENTO']);?> fa-stack-1x fa-inverse"></i>
				</span>
			</div>
			<div class="media-body">
				<h4 class="media-heading"><?php echo "<b>".fDtHoraEvento($result->fields['DTHORA_EVENTO_INI'],$result->fields['DTHORA_EVENTO_FIM'],"%d/%m")."</b>";?></h4>
				<p>
				<?php
				$info = "";
				if (trim($result->fields['INFO_ADIC']) != ""):
					$info .= "<i class=\"fa fa-question-circle\"></i>&nbsp;" .trim($result->fields['INFO_ADIC']);
				endif;
				if (trim($result->fields['DESC_LOCAL']) != ""):
					$info .= "<br/><i class=\"fa fa-map-marker\"></i>&nbsp;".trim($result->fields['DESC_LOCAL']);
				endif;
				if ($info != ""):
					echo "$info<br/>";
				endif;

				$endereco = trim($result->fields['DESC_LOGRADOURO']);
				if (trim($result->fields['NUM_LOGRADOURO']) != ""):
					$endereco .= ", ".trim($result->fields['NUM_LOGRADOURO']);
				endif;
				if (trim($result->fields['DESC_COMPLEMENTO']) != ""):
					$endereco .= " - ".trim($result->fields['DESC_COMPLEMENTO']);
				endif;
				if ($endereco != ""):
					echo "$endereco<br/>";
				endif;

				$cidade = "";
				if (trim($result->fields['DESC_BAIRRO']) != ""):
					$cidade .= trim($result->fields['DESC_BAIRRO']);
				endif;
				if (trim($result->fields['DESC_CIDADE']) != ""):
					if ($cidade != ""):
						$cidade .= " - ";
					endif;
					$cidade .= trim($result->fields['DESC_CIDADE']);
				endif;
				if (trim($result->fields['COD_UF']) != ""):
					if ($cidade != ""):
						$cidade .= " - ";
					endif;
					$cidade .= trim($result->fields['COD_UF']);
				endif;
				if ($cidade != ""):
					echo ($cidade)."<br/>";
				endif;

				if (trim($result->fields['tp_grupo']) != ""):
					echo "<i class=\"fa fa-users\"></i>&nbsp;".fTipoAlvo($result->fields['tp_grupo'])."<br/>";
				endif;

				if (trim($result->fields['ds']) != ""):
					echo "<i class=\"fa fa-user-secret\"></i>&nbsp;".$result->fields['ds']."<br/>";
				endif;

				?>
				</p>
			</div>
		</div>
		<?php
		$result->MoveNext();
	endwhile;
	?>

        <!-- Footer -->
	<?php @require_once("include/footer.php");?>

    </div>
    <!-- /.container -->

<?php @require_once("include/bottom_page.php");?>
