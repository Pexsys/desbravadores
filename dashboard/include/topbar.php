<nav class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin:0">
	<ul class="nav navbar-top-links navbar-right">
		<li class="dropdown" style="display:none" id="notifyTasks">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color:#777777">
				<i class="fas fa-tasks fa-fw"></i>&nbsp;<i class="fas fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu dropdown-tasks"></ul>
		</li>
		<li class="dropdown" id="notifyAlerts" style="display:none">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color:#777777">
				<i class="fas fa-bell fa-fw"></i><span id="notifyAlertsBadge" style="display:none"><span class="badge badge-notify"></span></span><i class="fas fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu dropdown-alerts"></ul>
		</li>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<span style="color:#777777">
					<?php 
						if (!is_null($_SESSION['USER']['sexo'])):
							echo "<i class=\"fas ". ( $_SESSION['USER']['sexo'] == "F" ? "fa-female" : "fa-male" )." fa-fw\"></i>";
						endif;
						echo titleCase(fAbrevia($_SESSION['USER']['ds_usuario']));
					?>&nbsp;
					<i class="fas fa-caret-down"></i>
				</span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="#" id="myBtnLogout"><i class="fas fa-sign-out-alt fa-fw"></i>&nbsp;Sair</a></li>
			</ul>
		</li>
	</ul>
	<div class="navbar-header navbar-left">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php" style="margin:0">
				<img class="img-responsive" src="<?php echo $GLOBALS['pattern']->getVD();?>img/d1-lg-tr-fb.svg" width="60" height="5" alt="<?php echo $GLOBALS['pattern']->getClubeDS(array("cl","db","nm"));?>">
			</a>
		</div>
	<?php @include_once("menu.php");?>
</nav>