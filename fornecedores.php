<?php
@require_once("include/functions.php");
fHeaderPage( array( PATTERNS::getVD()."css/index.css?" )
		   , array( PATTERNS::getVD()."js/index.js?") );
fConnDB();

function fGetClass($strTipoEvento){
	$eventClass = array(
		"ONI" => "fa-bus",
		"CAM" => "fa-truck",
		"BAR" => "fa-home",
		"LEN" => "fa-fire",
		"TEN" => "fa-chevron-up",
		"SIT" => "fa-tree",
		"CON" => "fa-industry",
		"COS" => "fa-female",
		"BAN" => "fa-flag",
		"OFI" => "fa-shopping-cart",
		"OUT" => "fa-info"
	);
	if (array_key_exists($strTipoEvento,$eventClass)):
		return $eventClass[$strTipoEvento];
	else:
		return fGetClass("OUT");
	endif;
}

function fGetType($strTipoEvento){
	$eventType = array(
		"ONI" => "&Ocirc;nibus",
		"CAM" => "Caminh&otilde;es",
		"BAR" => "Barracas",
		"LEN" => "Lenhas",
		"TEN" => "Tendas",
		"SIT" => "S&iacute;tios",
		"CON" => "Confec&ccedil;&otilde;es",
		"COS" => "Costureiras",
		"BAN" => "Bandeiras Oficiais",
		"OFI" => "Produtos Oficiais",
		"OUT" => "Outros"
	);
	if ( array_key_exists($strTipoEvento,$eventType ) ):
		return $eventType[$strTipoEvento];
	else:
		return fGetType("OUT");
	endif;
}

?><body>

    <!-- Navigation -->
	<?php @require_once("include/navbar.php");?>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1 class="page-header">Nossos Fornecedores
                    <small>Recomendados</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">In&iacute;cio</a></li>
                    <li class="active">Nossos Fornecedores</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->

	<?php
	$result = $GLOBALS['conn']->Execute( "
	  SELECT TP, COUNT(*) AS QT
	    FROM CAD_FORNECEDORES
	   WHERE FG_ATIVO = ?
	GROUP BY TP
	ORDER BY TP", Array( 'S' ) );

	foreach ($result as $ln):
	?>
	<div class="card">
	  <div class="header bg-brown">
	  	<span class="badge pull-right"><?php echo $ln['QT'];?></span>
		<h1 class="panel-title"><b><?php echo fGetType($ln['TP']);?></b></h1>
	  </div>
	  <div class="panel-body">
	  	<?php
	  	$rs = $GLOBALS['conn']->Execute( "
		  SELECT *
		    FROM CAD_FORNECEDORES
		   WHERE FG_ATIVO = ?
		     AND TP = ?
		ORDER BY NM", Array( 'S', $ln['TP'] ) );

		foreach ($rs as $rl):
		?>
		<div class="row media col-lg-6 col-md-6 col-sm-6 col-xs-6">
			<div class="pull-left">
				<span class="fa-stack fa-2x">
					  <i class="fa fa-circle fa-stack-2x text-danger"></i>
					  <i class="fa <?php echo fGetClass($rl['TP']);?> fa-stack-1x fa-inverse"></i>
				</span>
			</div>
			<div class="media-body">
				<h4 class="media-heading"><?php  echo "<b>".(trim($rl['NM']))."</b>";?></h4>
				<?php
				if (isset($rl['SRVS'])):
					echo "<i class=\"fa fa-question\"></i>&nbsp;".(trim($rl['SRVS']))."<br/>";
				endif;
				if (isset($rl['FONES'])):
					echo "<i class=\"fa fa-phone\"></i>&nbsp;".(trim($rl['FONES']))."<br/>";
				endif;
				if (isset($rl['SITE'])):
					echo "<i class=\"fa fa-sitemap\"></i>&nbsp;<a href=\"".(trim($rl['SITE']))."\">".(trim($rl['SITE']))."</a><br/>";
				endif;
				if (isset($rl['EMAIL'])):
					echo "<i class=\"fa fa-envelope-o\"></i>&nbsp;".(trim($rl['EMAIL']))."<br/>";
				endif;
				if (isset($rl['CONTATO'])):
					echo "<i class=\"fa fa-user\"></i>&nbsp;".(trim($rl['CONTATO']))."<br/>";
				endif;
				if (isset($rl['END'])):
					echo "<i class=\"fa fa-map-marker\"></i>&nbsp;".(trim($rl['END']))."<br/>";
				endif;
				?>
			</div>
		</div>
		<?php
	  	endforeach;
	  	?>
	  </div>
	</div>
	<?php
	endforeach;
	?>

    <!-- Footer -->
	<?php @require_once("include/footer.php");?>

    </div>
    <!-- /.container -->

<?php @require_once("include/bottom_page.php");?>
