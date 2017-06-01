<?php 
@require_once("include/functions.php");
fHeaderPage();
fConnDB();
?>
<body>

    <!-- Navigation -->
	<?php @include_once("include/navbar.php");?>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Unidades
                    <small>10 a 15 anos</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php">Home</a>
                    </li>
                    <li class="active">Unidades</li>
                </ol>
            </div>
        </div>
        <!-- /.row -->
        
        <?php
        $result = $GLOBALS['conn']->Execute( "
        	  SELECT *
        	    FROM TAB_UNIDADE 
        	   WHERE FG_ATIVA = 'S'
        	     AND TP IN ('F','M')
		");
        
        $a = array();
        foreach ($result as $ln):
            $a[] = array( $ln["ID"], !is_null($ln["DESCRIPTION"]) );
        endforeach;
        
        $arr = array();
        $ca = min(12,count($a));
        while (count($arr) < $ca):
            $pos = rand(0,count($a)-1);
            $arr[] = $a[$pos];
            array_splice($a, $pos, 1);
        endwhile;
        
        $qtd = 0;
        foreach( $arr as $k => $i ):
            if (++$qtd == 1):
                echo "<div class=\"row\">";
            endif;
            ?>
            <div class="col-md-2 img-portfolio">
                <a href="<?php echo ( $i[1] ? "unidade.php?id=".$i[0] : "#"); ?>">
                    <img class="img-responsive img-hover" src="report/img/unidade/<?php echo $i[0];?>_E.png" alt="">
                </a>
            </div>
            <?php
            if ($qtd == 6):
                echo "</div>";
                $qtd = 0;
            endif;
        endforeach;
        ?>

        <!-- Footer -->
		<?php @include_once("include/footer.php");?>

    </div>
    <!-- /.container -->

<?php @include_once("include/bottom_page.php");?>