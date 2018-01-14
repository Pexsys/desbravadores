<?php
@include_once("include/functions.php");
fHeaderPage( array( $GLOBALS['pattern']->getVD()."include/_core/css/index.css" )
		   , array( $GLOBALS['pattern']->getVD()."js/index.js") );
fConnDB();

function nextEvents(){
?>
 <div class="col-md-4">
	<div class="panel panel-danger">
		<div class="panel-heading"><h4><i class="fa fa-fw fa-calendar"></i>&nbsp;Pr&oacute;ximos Eventos</h4></div>
		<div class="panel-body">
			<?php
			$DATA_NOW = date('Y-m-d H:i:s');

			$query = "SELECT e.*, c.tp_grupo, t.ds
				    FROM CAD_EVENTOS e
			       LEFT JOIN RGR_CHAMADA c ON (c.ID_EVENTO = e.ID_EVENTO)
			       LEFT JOIN TAB_TP_UNIFORME t ON (t.ID = c.ID_TAB_TP_UNIFORME)
				   WHERE e.FLAG_PUBLICACAO = 'S' AND
					( e.DTHORA_EVENTO_INI >= ? OR ( e.DTHORA_EVENTO_FIM IS NOT NULL AND e.DTHORA_EVENTO_FIM >= ? ) )
				ORDER BY e.DTHORA_EVENTO_INI 
				   LIMIT 0, 3 ";
			$result = $GLOBALS['conn']->Execute( $query, Array( $DATA_NOW, $DATA_NOW ) );

			$MES_ANT = "";
			while (!$result->EOF):
				?>
					<p class="divider"><i class="fa fa-fw fa-hand-o-right"></i>
						<?php 
						echo "<b>".fDtHoraEvento($result->fields['DTHORA_EVENTO_INI'],$result->fields['DTHORA_EVENTO_FIM'])."</b>" ; 
						
						echo "<div style='margin-left:23px;margin-top:-10px;margin-bottom:15px'>" .$info = "";
						if (trim($result->fields['INFO_ADIC']) != ""):
							$info .= "<i class=\"fa fa-info-circle\"></i>&nbsp;" .trim($result->fields['INFO_ADIC']);
						endif;
						if (trim($result->fields['DESC_LOCAL']) != ""):
							$info .= "<br/><i class=\"fa fa-map-marker\"></i>&nbsp;".trim($result->fields['DESC_LOCAL']);
						endif;
						if ($info != ""):
							echo "$info<br/>";
						endif;
						
						$endereco = "";
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
							echo "<i class=\"fa fa-users\"></i>&nbsp;". (fTipoAlvo($result->fields['tp_grupo'])) ."<br/>";
						endif;
						
						if (trim($result->fields['ds']) != ""):
							echo "<i class=\"fa fa-user-secret\"></i>&nbsp;". ($result->fields['ds']) ."<br/>";
						endif;
						echo "</div>";
						?>
					</p>
				<?php
				$result->MoveNext();
			endwhile;
			?>
			<p><a class="btn btn-danger pull-right" href="agenda.php" role="button">Ver mais... &raquo;</a></p>
		</div>
	</div>
</div>
<?php
}

function myUnit(){
?>
    <div class="col-md-4">
    	<div class="panel panel-success">
    		<div class="panel-heading"><h4><i class="fa fa-fw fa-bullhorn"></i>&nbsp;Conheça a Unidade</h4></div>
    		<div class="panel-body">
    		<?php
                $result = $GLOBALS['conn']->Execute( "
            	  SELECT ID
            	    FROM TAB_UNIDADE 
            	   WHERE FG_ATIVA = 'S'
            	     AND TP IN ('F','M')
            	     AND DESCRIPTION IS NOT NULL
                ");
                
                $a = array();
                foreach ($result as $ln):
                    $a[] = $ln["ID"];
                endforeach;
                $pos = rand(0,count($a)-1);
                ?>
                <div class="col-md-12 col-xs-12">
                    <a href="unidade.php?id=<?php echo $a[$pos];?>">
                        <img class="img-responsive img-hover img-related" src="report/img/unidade/<?php echo $a[$pos];?>_E.png" alt="">
                    </a>
                </div>
    		</div>
    	</div>
    </div>
<?php
}

function about(){
	?>
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading"><h4><i class="fa fa-fw fa-history"></i>&nbsp;Voltando no tempo...</h4></div>
			<div class="panel-body">
				<p>Quem são os <?php echo $GLOBALS['pattern']->getClubeDS(array("nm"));?>? Quer saber como tudo começou? A história real contada em detalhes.</p>
				<p><a class="btn btn-info pull-right" href="about.php" role="button">Saiba mais... &raquo;</a></p>
			</div>
		</div>
	</div>
<?php
}
?>
<body>

    <!-- Navigation -->
	<?php @include_once("include/navbar.php");?>

    <!-- Navigation -->
	<?php @include_once("include/header.php");?>		

    <!-- Page Content -->
    <div class="container">

        <!-- Marketing Icons Section -->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header">
                    Bem vindo ao <?php echo $GLOBALS['pattern']->getClubeDS(array("cl","nm"));?>!
                </h3>
            </div>
            <?php
			nextEvents();
           
            if ( date("m") < 3 ):
				insDocs();
				about();
                myUnit();
            else:
                myUnit();
				insDocs();
				about();
			endif;	
            ?>
        </div>
        <!-- /.row -->

        <!-- Portfolio Section -->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header">Arquivos</h3>
            </div>
			<div class="col-md-4">
			<?php fListFanfarra("docs/fanfarra/","&nbsp;Repert&oacute;rio Fanfarra",".pdf");?>
            </div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading"><h4><i class="fa fa-fw fa-file-text"></i>&nbsp;Capas de Especialidades</h4></div>
					<div class="panel-body">
						<p>Aqui nesta se&ccedil;&atilde;o, voc&ecirc; poder&aacute; em poucos cliques acessar a ferramenta de gera&ccedil;&atilde;o e gerar todas as capas das especialidades que precisa, no padr&atilde;o exigido por nosso regional.</p>
						<p><a class="btn btn-info pull-right" href="capas.php" role="button">Gerar Capas</a></p>
					</div>
				</div>
				<div class="panel panel-primary">
					<div class="panel-heading"><h4><i class="fa fa-fw fa-film"></i>&nbsp;V&iacute;deos</h4></div>
					<div class="panel-body">
						<div class="media">
							<a class="media-left" href="http://www.youtube.com/embed/VIi2gv2lRnk" target="_new">
								<i class="fa fa-play"></i>
							</a>
							<div class="media-body">
								<h4 class="media-heading">Comemora&ccedil;&atilde;o do Dia Mundial 2014</h4>
							</div>
						</div>
						<div class="media">
							<a class="media-left" href="http://new.livestream.com/accounts/6786166/events/2683751/videos/53302646/player?autoPlay=false" target="_new">
								<i class="fa fa-play"></i>
							</a>
							<div class="media-body">
								<h4 class="media-heading">Encena&ccedil;&atilde;o da Fornalha Ardente</h4>
							</div>
						</div>
					</div>
				</div>
            </div>
			<div class="col-md-4">
			<?php echo fListDocumentos("docs/outros/","<h4><i class=\"fa fa-fw fa-pencil\"></i>&nbsp;Outros Documentos</h4>",".doc;.docx;.xls;.xlsx;.pdf;","panel-primary","h5");?>
			</div>
		</div>
        <!-- /.row -->

        <!-- Footer -->
		<?php @include_once("include/footer.php");?>

    </div>
    <!-- /.container -->
	
<?php @include_once("include/bottom_page.php");?>