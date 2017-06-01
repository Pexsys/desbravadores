<!-- Header Carousel-->
<header id="myCarousel" class="carousel slide" data-ride="carousel">
	<?php
	$relativePath = "img/carrousel/index/";
	$extentions = ".jpg.jpeg.gif.png";
	$capa = $GLOBALS['VirtualDir'] . $relativePath;
	if ($_SERVER['SERVER_NAME'] == "192.168.1.249" || $_SERVER['SERVER_NAME'] == "localhost"):
		$document_root = substr($_SERVER['DOCUMENT_ROOT'],0,strlen($_SERVER['DOCUMENT_ROOT'])-1);
		$fisico_repertorio = $document_root . $capa;
	else:
		$document_root = $_SERVER['DOCUMENT_ROOT'];
		$fisico_repertorio = $relativePath;
	endif;
	$dirRepertorio = array();
	if (is_dir($fisico_repertorio)):
		if ($handle = opendir($fisico_repertorio)):
			while (false !== ($file = readdir($handle))):
				if ($file != "." && $file != ".." && is_dir($fisico_repertorio.$file) ):
					$dirRepertorio[] = $file;
				endif;
			endwhile;
			closedir($handle);
			$qtdLin = count($dirRepertorio);
			if ($qtdLin > 0):
				sort($dirRepertorio);
				foreach ( $dirRepertorio as $k => $v ):
					$fileDoc = str_replace('_', ' ', $v);
					$capaFiles = array();
					if ($handle = opendir($fisico_repertorio.$fileDoc)):
						while (false !== ($file = readdir($handle))):
							if ($file != "." && $file != ".."):
								$ext = strtolower(substr($file,strlen($file)-4,4));
								if (!(strpos($extentions,$ext) === false)):
									$capaFiles[] = $file;
								endif;
							endif;
						endwhile;
						closedir($handle);
						$qtdFiles = count($capaFiles);
						if ($qtdFiles > 0):
							sort($capaFiles);
							$dirRepertorio[$k] = array( $v => $capaFiles[ rand(0, count($capaFiles)-1 ) ] );
						endif;
					endif;
				endforeach;
				
				$seq = 0;
				echo "<!-- Indicators -->";
				echo "<ol class=\"carousel-indicators\">";
				foreach ( $dirRepertorio as $k => $v ):
					foreach ( $dirRepertorio[$k] as $ki => $vi ):
						echo "<li data-target=\"#myCarousel\" data-slide-to=\"$seq\"".($i==$seq?" class=\"active\"":"")."></li>";
						$seq++;
					endforeach;
				endforeach;
				echo "</ol>";
				
				$seq = 0;
				echo "<!-- Wrapper for slides -->";
				echo "<div class=\"carousel-inner\" role=\"listbox\">";
				foreach ( $dirRepertorio as $k => $v ):
					foreach ( $dirRepertorio[$k] as $ki => $vi ):
						echo "<div class=\"item".($seq==0?" active":"")."\">";
						echo "<img src=\"img/carrousel/index/$ki/$vi\" alt=\"$ki\" width=\"100%\" height=\"100%\"/>";
						echo "<div class=\"carousel-caption\">";
						echo "<h1>$ki</h1>";
						echo "</div>";
						echo "</div>";
						++$seq;
					endforeach;
				endforeach;

			endif;
		endif;
	endif;
	?>

	<!-- Controls -->
	<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Pr&oacute;ximo</span>
	</a>
	<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Anterior</span>
	</a>
</header>