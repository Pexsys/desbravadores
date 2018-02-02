<?php
session_start();
@require_once("../functions.php");
@require_once("../../rules/profile.php");

$arrPerfil = $_SESSION['USER']['perfil'];
$temPerfil = sizeof($arrPerfil) > 0;
if (!$temPerfil):
	session_destroy();
	header("Location: index.php");
	exit;
endif;
fHeaderPage( array( "_core/css/dashboard.css" ) );
$idParam = fRequest("id");
?>
<body>
<!-- Navigation -->
<?php @include_once("../navbar.php");?>
<br/>
<div class="container-fluid">
	<div class="row content">
		<div class="col-sm-3 sidenav">
			<br/>
			<h5>ADMINISTRA&Ccedil;&Atilde;O</h5>
			<hr/>
			<ul class="nav nav-pills nav-stacked">
				<?php
				$dashboardActive = "";
				foreach ($arrPerfil as $key => $value):
					$url = $value["url"]."?id=$key";
					$opt = $value["opt"];
					if ($idParam == $key):
						$active = " class=\"active\"";
						$dashboardActive = $opt;
					else:
						$active = "";
					endif;
					echo "<li$active><a href=\"$url\">$opt</a></li>";
				endforeach;
				?>
			</ul>
			<br/>
		</div>
		<?php if ($dashboardActive == "Agenda"):?>
		<div class="col-sm-9">
			<?php
				$DATA_NOW = date('Y-m-d H:i:s');
				$result = CONN::get()->Execute("
				SELECT *
				FROM CAD_EVENTOS 
				WHERE ( DTHORA_EVENTO_INI >= '$DATA_NOW' OR 
					   ( DTHORA_EVENTO_FIM IS NOT NULL AND DTHORA_EVENTO_FIM >= '$DATA_NOW' ) 
					  )
				ORDER BY DTHORA_EVENTO_INI ");
				$MES_ANT = "";
				do {
					//$dtEventoIni = new DateTime();
					//echo $dtEventoIni;
					$MES_ATU = ucfirst(strftime("%B", strtotime($result->fields['DTHORA_EVENTO_INI'])));
					if ($MES_ATU != $MES_ANT):
						$MES_ANT = $MES_ATU;
						?>
						<div class="col-sm-3">
						  <div class="well">
							<h4><?php echo $MES_ANT;?></h4>
						  </div>
						</div>
						<?php 
					endif;
					$result->MoveNext();
				} while (!$result->EOF);
			?>
		</div>
		<?php else:?>
		<div class="col-sm-9">
		  <div class="well">
			<h4>Dashboard</h4>
			<p>Some text..</p>
		  </div>
		  <div class="row">
			<div class="col-sm-3">
			  <div class="well">
				<h4>Users</h4>
				<p>1 Million</p> 
			  </div>
			</div>
			<div class="col-sm-3">
			  <div class="well">
				<h4>Pages</h4>
				<p>100 Million</p> 
			  </div>
			</div>
			<div class="col-sm-3">
			  <div class="well">
				<h4>Sessions</h4>
				<p>10 Million</p> 
			  </div>
			</div>
			<div class="col-sm-3">
			  <div class="well">
				<h4>Bounce</h4>
				<p>30%</p> 
			  </div>
			</div>
		  </div>
		  <div class="row">
			<div class="col-sm-4">
			  <div class="well">
				<p>Text</p> 
				<p>Text</p> 
				<p>Text</p> 
			  </div>
			</div>
			<div class="col-sm-4">
			  <div class="well">
				<p>Text</p> 
				<p>Text</p> 
				<p>Text</p> 
			  </div>
			</div>
			<div class="col-sm-4">
			  <div class="well">
				<p>Text</p> 
				<p>Text</p> 
				<p>Text</p> 
			  </div>
			</div>
		  </div>
		  <div class="row">
			<div class="col-sm-8">
			  <div class="well">
				<p>Text</p> 
			  </div>
			</div>
			<div class="col-sm-4">
			  <div class="well">
				<p>Text</p> 
			  </div>
			</div>
		  </div>
		</div>
		<?php endif;?>
	</div>
	<hr>
</div>
<!-- Footer -->
<?php @include_once("../footer.php");?>
<!-- /.container -->
<?php @include_once("../bottom_page.php");?>