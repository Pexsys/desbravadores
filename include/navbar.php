<?php 
$showLogin = true;
$activeLogin = ($showLogin && !$temPerfil);
?><nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="row">
			<div class="col-md-4 col-sm-5">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only"><?php echo $GLOBALS['pattern']->getClubeDS(array("nm"));?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="active" href="index.php">
					<!-- Logo area -->
					<div class="logo"><img class="img-responsive" width="110" src="<?php echo $GLOBALS['pattern']->getVD();?>img/d1-lg-tr-fb.svg" alt="<?php echo $GLOBALS['pattern']->getClubeDS(array("cl","db","nm"));?>"></div>
				</a>
			</div>
			<div class="col-md-8 col-sm-7">
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a href="about.php">História</a>
						</li>
						<li>
							<a href="agenda.php">Eventos</a>
						</li>
						<li>
							<a href="unidades.php">Unidades</a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown" data-toggle="dropdown" style="background-color:#222">Outros<b class="caret"></b></a>
							<ul class="dropdown-menu">

								<li>
									<a href="fornecedores.php">Fornecedores</a>
								</li>
								<li>
									<a href="capas.php">Gerar Capas</a>
								</li>
							</ul>
						</li>
						<?php
						/*
						<li class="active">
							<a href="contact.php">Fale conosco</a>
						</li>
						<li class="dropdown active">
							<a href="#" class="dropdown active" data-toggle="dropdown">Arquivos <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li>
									<a href="portfolio-1-col.php">Inscri&ccedil;&otilde;es 2016</a>
								</li>
								<li>
									<a href="portfolio-2-col.php">Outros Documentos</a>
								</li>
								<li>
									<a href="portfolio-3-col.php">Capas de Especialidades</a>
								</li>
								<li>
									<a href="portfolio-4-col.php">Repert&oacute;rio Fanfarra</a>
								</li>
								<li>
									<a href="portfolio-item.php">V&iacute;deos</a>
								</li>
							</ul>
						</li>
						<li class="dropdown active">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Unidades <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li>
									<a href="portfolio-1-col.php">1 Column Portfolio</a>
								</li>
								<li>
									<a href="portfolio-2-col.php">2 Column Portfolio</a>
								</li>
								<li>
									<a href="portfolio-3-col.php">3 Column Portfolio</a>
								</li>
								<li>
									<a href="portfolio-4-col.php">4 Column Portfolio</a>
								</li>
								<li>
									<a href="portfolio-item.php">Single Portfolio Item</a>
								</li>
							</ul>
						</li>
						<li class="dropdown active">
							<a class="dropdown-toggle active" data-toggle="dropdown" href="#">
								<i class="far fa-user fa-fw"></i>  <i class="far fa-caret-down"></i>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#" id="myBtnLogout"><i class="far fa-sign-out fa-fw"></i>&nbsp;Logout</a></li>
							</ul>
						</li>
						<li><a href="#"><span class="glyphicon glyphicon-user"></span> Cadastre-se</a></li>
						*/
						?>
						<?php
						if ( $showLogin ):
							if ( $activeLogin ):
								echo "<li><button type=\"button\" id=\"myBtnLogin\" class=\"btn btn-success btn-sm navbar-btn\"><span class=\"glyphicon glyphicon-log-in\"></span>&nbsp;Acesso Restrito</button></li>";
							else:
								echo "<li><button type=\"button\" id=\"myBtnLogout\" class=\"btn btn-danger btn-sm navbar-btn\"><span class=\"glyphicon glyphicon-log-out\"></span>&nbsp;Sair</button></li>";
							endif;
						endif;
						?>
					</ul>
				</div>
			</div>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container -->
</nav>
<?php
if ( $showLogin ):
	@include_once("login.php");
endif;
?>