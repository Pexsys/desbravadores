<?php 
@require_once("include/functions.php");


$result = CONN::get()->Execute( "
  SELECT *
    FROM TAB_UNIDADE 
   WHERE ID = ?
     AND DESCRIPTION IS NOT NULL
ORDER BY IDADE, TP", Array( fRequest("id") ) );
	
if ($result->EOF):
    header('location: unidades.php');    
endif;
fHeaderPage();
?>
<body>

    <!-- Navigation -->
	<?php @include_once("include/navbar.php");?>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Unidade
                    <small><?php echo ($result->fields["DS"]);?></small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a>
                    </li>
                    <li class="active">Unidade</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->
        
         <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-6">
        				<img class="img-responsive" src="report/img/unidade/<?php echo $result->fields["ID"];?>_D.png" alt="">
                    </div>
                    <div class="col-md-6">
                        <h3>Atributos</h3>
                        <ul>
                            <li><b>Idade</b>:&nbsp;<?php echo $result->fields["IDADE"];?> anos</li>
                            <li><b>G&ecirc;nero</b>:&nbsp;<?php echo ( $result->fields["TP"] == "F" ? "Feminino" : "Masculino" );?></li>
                            <li><b>Cor do g&ecirc;nero</b>:&nbsp;<i class="fas fa-stop" aria-hidden="true" style="color:<?php echo $result->fields["CD_COR_GENERO"];?>"></i></li>
                            <li><b>Cor da unidade</b>:&nbsp;<i class="fas fa-stop" aria-hidden="true" style="color:<?php echo $result->fields["CD_COR"];?>"></i></li>
                            <li><b>Grito de Guerra</b>:<br/><i><?php echo ($result->fields["GRITO"]);?></i></li>
                            <?php 
                            if (!empty($result->fields["HISTORY"])):
                                echo "<li><b>Hist&oacute;ria e Significados</b>:&nbsp;{$result->fields["HISTORY"]}</li>";
                            endif;
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
        	            <h3>Caracter&iacute;sticas da Ave Símbolo</h3>
        	            <?php echo ($result->fields["DESCRIPTION"]);?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h3>Fazendo história</h3>
                <ul class="list-group">
                <?php
                $anoAnt = "";
                $strmembros = "";
                $anos = CONN::get()->Execute("
                    SELECT ca.NR_ANO, ca.CD_CARGO, cp.NM
                	  FROM CAD_ATIVOS ca
                      INNER JOIN CAD_MEMBRO cm ON (ca.ID_CAD_MEMBRO = cm.ID)
                      INNER JOIN CAD_PESSOA cp ON (cp.ID = cm.ID_CAD_PESSOA)
                	 WHERE ca.ID_UNIDADE = ?
                	ORDER BY ca.NR_ANO DESC, ca.CD_CARGO, cp.NM
                ", Array( fRequest("id") ) );
                foreach ($anos as $k => $f):
                    $color = $f["NR_ANO"] == date('Y') ? " style=\"background-color:#28a745\"" : "";
                    if ($anoAnt != $f["NR_ANO"]):
                        if ($strmembros != ""):
                            echo "$strmembros</li>";
                            $strmembros = "";
                        endif;
                        echo "<li class=\"list-group-item\"><span class=\"badge pull-left badge-success\"$color>".$f["NR_ANO"]."</span>&nbsp;";
                        $anoAnt = $f["NR_ANO"];
                    endif;
                    $strmembros .= (empty($strmembros)?"":", ");
                    $a = explode(" ",$f["NM"]);
                    $strmembros .= (fStrStartWith($f["CD_CARGO"],"2")?"<u>".titleCase($a[0])."</u>":titleCase($a[0]));
                endforeach;
                echo "$strmembros</li>";
                ?>
                </ul>
            </div>
        </div>

        <!-- Related Projects Row -->
        <div class="row">

            <div class="col-lg-12">
                <h3 class="page-header">Outras unidades</h3>
            </div>
            
            <?php
            $result = CONN::get()->Execute( "
        	  SELECT *
        	    FROM TAB_UNIDADE 
        	   WHERE TP IN ('F','M')
        	     AND ID <> ?
        	  ORDER BY ID
            ", Array( $result->fields["ID"] ) );
            
            $a = array();
            foreach ($result as $ln):
                $a[] = $ln["ID"];
            endforeach;
            
            $arr = array();
            $ca = min(12,count($a));
            while (count($arr) < $ca):
                $pos = rand(0,count($a)-1);
                $arr[] = $a[$pos];
                array_splice($a, $pos, 1);
            endwhile;
            
            foreach( $arr as $i ):
                ?>
                <div class="col-md-1 col-xs-4">
                    <a href="unidade.php?id=<?php echo $i;?>">
                        <img class="img-responsive img-hover img-related" src="report/img/unidade/<?php echo $i;?>_E.png" alt="">
                    </a>
                </div>
                <?php
            endforeach;
            ?>

        </div>
        <!-- /.row -->

        <!-- Footer -->
		<?php @include_once("include/footer.php");?>

    </div>
    <!-- /.container -->

<?php @include_once("include/bottom_page.php");?>
