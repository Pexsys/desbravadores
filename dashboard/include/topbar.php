<nav class="navbar navbar-inverse navbar-static-top" role="navigation" style="margin:0">
	<ul class="nav navbar-top-links navbar-right">
		<?php
		$rs = CONN::get()->Execute("
			SELECT ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO, COUNT(*) AS QT_REQ
			FROM CON_APR_PESSOA
			WHERE ID_CAD_PESSOA = ? AND DT_CONCLUSAO IS NULL
			GROUP BY ID_CAD_PESSOA, ID_TAB_APREND, TP_ITEM, CD_AREA_INTERNO, CD_ITEM_INTERNO, DS_ITEM, DT_INICIO
			ORDER BY TP_ITEM, CD_ITEM_INTERNO
		", array( $_SESSION['USER']['id_cad_pessoa'] ) );
		if (!$rs->EOF):
			echo "<li class=\"dropdown\" id=\"notifyTasks\">
					<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\" style=\"color:#777777\">
						<i class=\"fas fa-tasks fa-fw\"></i><span class=\"badge badge-notify\">".$rs->RecordCount()."</span><i class=\"fas fa-caret-down\"></i>
					</a>";
			echo "<ul class=\"dropdown-menu dropdown-tasks\">";
			foreach ($rs as $ks2 => $det):
				$qtdReq = $det["QT_REQ"];
				$tabAprID = $det["ID_TAB_APREND"];
				$pct = 0;
				$qtd = 0;
				$rc = CONN::get()->Execute("
					SELECT COUNT(*) AS QT_COMPL
					FROM CON_APR_PESSOA
					WHERE ID_CAD_PESSOA = ?
					AND ID_TAB_APREND = ?
					AND DT_ASSINATURA IS NOT NULL
				", array($_SESSION['USER']['id_cad_pessoa'], $tabAprID) );
				if (!$rc->EOF):
					$qtd = $rc->fields["QT_COMPL"];
				endif;
				$pctC = floor( ( $qtd / $qtdReq ) * 100);
				$pctP = floor( 100 - $pctC);
				$qtdP = ($qtdReq-$qtd);

				echo "<li><a href=\"".PATTERNS::getVD()."dashboard/index.php?id=16\">";
				echo "<div class=\"col-lg-12\" style=\"margin-bottom:-15px\">";
				echo "<label class=\"control-label\"><i class=\"".getIconAprendizado( $det["TP_ITEM"], $det["CD_AREA_INTERNO"] )."\"></i>&nbsp;".titleCase($det["DS_ITEM"])."</label>";
				echo "<div class=\"progress\" style=\"margin-bottom:0px;cursor:pointer\">";
				echo "<div class=\"progress-bar progress-bar-success\" role=\"progressbar\" style=\"width:$pctC%\">$pctC%</div>";
				echo "<div class=\"progress-bar progress-bar-danger\" role=\"progressbar\" style=\"width:$pctP%\">$pctP%</div>";
				echo "</div></a></li>";
			endforeach;
			echo "</ul>";
			echo "</li>";
		endif;
		?>
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
				<img class="img-responsive" src="<?php echo PATTERNS::getVD();?>img/d1-lg-tr-fb.svg" width="60" height="5" alt="<?php echo PATTERNS::getClubeDS(array("cl","db","nm"));?>">
			</a>
		</div>
	<?php @include_once("menu.php");?>
</nav>