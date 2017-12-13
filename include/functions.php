<?php
ini_set('memory_limit','200M');
error_reporting (E_ALL); // & ~ E_NOTICE & ~ E_DEPRECATED
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

global $pattern, $conn, $DBType, $DBServerHost, $DBUser, $DBPassWord, $DBDataBase;
@require_once("_patterns.php");
@require_once("_core/lib/adodb5/adodb.inc.php");
@require_once("_core/lib/dbconnect/_base.php");
@require_once("profile.php");

function zeroSizeID(){
	if (!isset($_SESSION['USER']['sizeID'])):
		session_start();
		$rs = $GLOBALS['conn']->Execute("SELECT COUNT(*) AS qtd FROM CAD_PESSOA");
		if (!$rs->EOF):
			$_SESSION['USER']['sizeID'] = strlen($rs->fields['qtd']);
		endif;
	endif;
	return $_SESSION['USER']['sizeID'];
}

function responseMethod(){
    header('Content-type: application/json');
	// Getting the json data from the request
	$response = '';
	
	$json_data = json_decode( json_encode( empty($_POST) ? $_GET : $_POST ) );
	// Checking if the data is null..
	if ( is_null( $json_data ) ):
		$response = json_encode( array( "status" => -1, "message" => "Insufficient paramaters!") );
	elseif ( empty( $json_data->{'MethodName'} ) ):
		$response = json_encode( array( "status" => 0, "message" => "Invalid function name!" ) );
	else:
		$methodName = $json_data->MethodName;
		if ( isset( $json_data->{'data'} ) ):
			$response = $methodName( objectToArray( $json_data->{'data'} ) );
		else:
			$response = $methodName();
		endif;
	endif;    
	echo json_encode($response);
}

function fRequest($pVar){
	if (isset($_GET[$pVar])) return $_GET[$pVar];
	if (isset($_POST[$pVar])) return $_POST[$pVar];
	return "";
}

function fTipoAlvo( $pTipoAlvo ) {
	$tipoAlvo = array(
		"T" => "TODOS",
		"D" => "DESBRAVADORES",
		"I" => "DIRETORIA"
	);
	if ( !isset($pTipoAlvo) ):
		return $tipoAlvo;
	else:
		return $tipoAlvo[$pTipoAlvo];
	endif;
}

function fHeaderPage( $aCssFiles = NULL, $aJsFiles = NULL ){
?>
<!DOCTYPE html>
<html>
<head>
<title>Clube Pioneiros - IASD Capão Redondo</title>
<?php @require_once("_metaheader.php");?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/css/modern-business.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<?php
if (isset($aCssFiles)):
	foreach ($aCssFiles as &$file):
	?><link href="<?php echo $file;?>" rel="stylesheet" type="text/css"><?php 
	endforeach;
endif;
?>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/formValidation/formValidation.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/formValidation/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>include/_core/js/bootstrap-dialog.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['pattern']->getVD();?>js/functions.lib.js<?php echo "?".microtime();?>"></script>
<script>jsLIB.rootDir = '<?php echo $GLOBALS['pattern']->getVD();?>';</script>
<?php
if (isset($aJsFiles)):
	foreach ($aJsFiles as &$file):
	?><script type="text/javascript" src="<?php echo $file;?>"></script><?php
	endforeach;
endif;
?>
</head>
<?php
}

function fConnDB(){
	try{
		$GLOBALS['conn'] = ADONewConnection($GLOBALS['DBType']);
		$GLOBALS['conn']->SetCharSet('utf8');
		$GLOBALS['conn']->Connect($GLOBALS['DBServerHost'],$GLOBALS['DBUser'],$GLOBALS['DBPassWord'],$GLOBALS['DBDataBase']);
		$GLOBALS['conn']->SetFetchMode(ADODB_FETCH_ASSOC);
		return true;
	}catch (Exception $e){
		return false;
	}
	return false;
}

function fDescMes($cMes){
	return (array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro")[$cMes-1]);
}

function fStrZero($n,$q){
	return str_pad($n, $q, "0", STR_PAD_LEFT);
}

function fStrToDate($pValue, $pic = "Y-m-d H:i") {
	$pValue = str_replace("/","-",$pValue);
	return date($pic, strtotime($pValue) );
}

function fConcatNoEmpty($sStr, $sSep, $sNew){
	return ( !empty($sStr) ? $sStr . $sSep : "" ) . $sNew;
}

function fReturnStringNull($s,$default = null){
	if ( isset($s) && trim($s) !== "" ):
		return $s;
	endif;
	return $default;
}

function fReturnNumberNull($n,$default = null){
	if ( isset($n) && is_numeric($n) ):
		return $n;
	endif;
	return $default;
}

function getDateNull($vl){
	if ( !isset($vl) || empty($vl) || is_null($vl) ):
		return null;
	endif;
	return fStrToDate($vl,"Y-m-d");
}

function fMontaCarrousel($relativePath,$extentions){
	$capa = $GLOBALS['pattern']->getVD() . $relativePath;

	$document_root = $_SERVER['DOCUMENT_ROOT'];
	$fisico_capas = $aDocumentos[$nLocais][0];

	$capaFiles = array();
	if (is_dir($fisico_capas)):
		if ($handle = opendir($fisico_capas)):
			while (false !== ($file = readdir($handle))):
				if ($file != "." && $file != ".." && !is_file($file)):
					$ext = strtolower(substr($file,strlen($file)-4,4));
					if (!(strpos($extentions,$ext) === false)):
						$capaFiles[] = $file;
					endif;
				endif;
			endwhile;
			closedir($handle);

			if (count($capaFiles) > 1):
				sort($capaFiles);

				$maxCars = 6;
				$minPerCar = 2;
				$qtdFiles = count($capaFiles);
				$qtd = min( $maxCars, floor( $qtdFiles / $minPerCar ) );
				$qtdMax = max( $minPerCar, floor( $qtdFiles / $qtd ) );

				$aCarrousel = array();
				for ($i=0;$i<$qtd;$i++):
					$aCarrousel[$i] = $qtdMax;
				endfor;
				$i = 0;
				while ( array_sum($aCarrousel) < $qtdFiles ):
					$aCarrousel[$i]++;
					if ( $i++ > count($aCarrousel) ):
						$i = 0;
					endif;
				endwhile;
				
				$icF = 0;
				for ($c=0;$c<count($aCarrousel);$c++):
					echo "<div class=\"col-md-2\">";
					echo "<div id=\"carousel-example-generic$c\" class=\"carousel slide\" data-ride=\"carousel\">";

					echo "<!-- Indicators -->";
					echo "<ol class=\"carousel-indicators\">";
					for ($x=0;$x<$aCarrousel[$c];$x++):
					echo "<li data-target=\"#carousel-example-generic$c\" data-slide-to=\"$x\"". ($x == 0 ? " class=\"active\"" : "") ."></li>";
					endfor;
					echo "</ol>";
					
					echo "<!-- Wrapper for slides -->";
					echo "<div class=\"carousel-inner\" role=\"listbox\">";
					
					for ($x=0;$x<$aCarrousel[$c];$x++):
					echo "<div class=\"item". ($x == 0 ? " active" : "") ."\">";
					echo "<img src=\"".$capa.$capaFiles[$icF++]."\" alt=\"$x\" width=\"100%\" height=\"100%\"/>";
					echo "</div>";
					endfor;
					echo "</div>";
					
					echo "<!-- Controls -->";
					echo "<a class=\"left carousel-control\" href=\"#carousel-example-generic$c\" role=\"button\" data-slide=\"prev\">";
					echo "<span class=\"glyphicon glyphicon-chevron-left\" aria-hidden=\"true\"></span>";
					echo "<span class=\"sr-only\">Previous</span>";
					echo "</a>";
					echo "<a class=\"right carousel-control\" href=\"#carousel-example-generic$c\" role=\"button\" data-slide=\"next\">";
					echo "<span class=\"glyphicon glyphicon-chevron-right\" aria-hidden=\"true\"></span>";
					echo "<span class=\"sr-only\">Next</span>";
					echo "</a>";
					
					echo "</div>";
					echo "</div>";
				endfor;
			endif;
		endif;
	endif;
}

function insDocs(){
	?>
	<div class="col-md-6 col-sm-9 col-lg-4">
	<?php fListDocumentos("docs/inscricoes/","<h4><i class=\"fa fa-fw fa-pencil\"></i>&nbsp;Inscri&ccedil;&otilde;es ".date('Y')."</h4>",".pdf", ( date("m") < 4 ? "panel-danger" : "panel-warning" ) ,"h4");?>
    </div>
<?php   
}

function fListDocumentos($relativePath,$title,$extentions,$classPanel,$tagItem){
	$capa = $GLOBALS['pattern']->getVD() . $relativePath;
	$capa_img = $GLOBALS['pattern']->getVD() . $relativePath."img/";
	
	$document_root = $_SERVER['DOCUMENT_ROOT'];
	$fisico_capas = dirname(dirname(__FILE__)) . "/$relativePath";
	$fisico_img = $fisico_capas . "img/";

	$capaFiles = array();
	if (is_dir($fisico_capas)):
		if ($handle = opendir($fisico_capas)):
			while (false !== ($file = readdir($handle))):
				if ($file != "." && $file != ".." && !is_file($file)):
					$ext = strtolower(substr($file,strlen($file)-4,4));
					if (!(strpos($extentions,$ext) === false)):
						$capaFiles[] = $file;
					endif;
				endif;
			endwhile;
			closedir($handle);

			if (count($capaFiles) > 0):
				sort($capaFiles);
				echo "<div class=\"panel $classPanel\">";
				echo "<div class=\"panel-heading\">$title</div>";
				echo "<div class=\"panel-body\">";
				//$pos = rand(0, count($capaFiles)-1);

				$qtdCol = 7;
				$qtdLin = ceil(count($capaFiles) / $qtdCol);
				$pos = 0;
				$nFalta = count($capaFiles);
				for ($i=1;$i<=$qtdLin;$i++):
					for ($j=1;$j<=min($qtdCol,$nFalta);$j++):
						$fileDoc = $capaFiles[$pos];
						$midFile = substr($fileDoc,0,strlen($fileDoc)-4);

						$link = $capa.$fileDoc;
						$fileImg = $capa_img."doc.jpg";
						if (is_file($fisico_img.$midFile.".jpg")):
							$fileImg = $capa_img.$midFile.".jpg";
						elseif (is_file($fisico_img.$midFile.".png")):
							$fileImg = $capa_img.$midFile.".png";
						elseif (is_file($fisico_img.$midFile.".gif")):
							$fileImg = $capa_img.$midFile.".gif";
						endif;
						$desc = preg_replace('/_/', ' ', $midFile);
						?>
						<div class="media">
							<a href="<?php echo $link;?>" target="_new">
							  <div class="media-left">
								  <img class="media-object" src="<?php echo $fileImg;?>" width="22px" height="22px" border="0">
							  </div>
							  <div class="media-body">
								<?php echo "<$tagItem class=\"media-heading\">$desc</$tagItem>";?>
							  </div>
							</a>
						</div>
						<?php
						$pos++;
					endfor;
					$nFalta = count($capaFiles) - $pos;
				endfor;
				
				if ( $relativePath == "docs/outros/" ):
					?>
					<div class="media">
						<a href="http://desbravadores.org.br.s3.amazonaws.com/materiais/2013/RUD.pdf" target="_new">
						  <div class="media-left">
							  <img class="media-object" src="<?php echo $capa_img?>reguladesb.jpg" alt="Regulamento de Uniformes Desbravadores, Aventureiros e Ministério Jovem" width="22px" height="22px" border="0">
						  </div>
						  <div class="media-body">
							<?php echo "<$tagItem class=\"media-heading\">Regulamento de Uniformes</$tagItem>";?>
						  </div>
						</a>
					</div>
					<?php
				endif;
				
				echo "</div>";
				echo "</div>";
			endif;
		endif;
	endif;
}

function fListFanfarra($relativePath,$title,$extentions){
	$capa = $GLOBALS['pattern']->getVD() . $relativePath;
	$capa_img = $GLOBALS['pattern']->getVD() . "img/";

	$document_root = $_SERVER['DOCUMENT_ROOT'];
	$fisico_repertorio = $relativePath;

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
				?>
				<div class="panel panel-primary">
				<div class="panel-heading"><h4><i class="fa fa-fw fa-music"></i><?php echo $title;?></h4></div>
				<div class="panel-body">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<?php
				for ($i=0;$i<$qtdLin;$i++):
					$fileDoc = str_replace('_', ' ', $dirRepertorio[$i]);
					$capaFiles = array();
					if ($handle = opendir($fisico_repertorio.$dirRepertorio[$i])):
						while (false !== ($file = readdir($handle))):
							if ($file != "." && $file != ".."):
								$ext = strtolower(substr($file,strlen($file)-4,4));
								if (!(strpos(".pdf;",$ext) === false)):
									$capaFiles[] = $file;
								endif;
							endif;
						endwhile;
						closedir($handle);
						$qtdFiles = count($capaFiles);
						if ($qtdFiles > 0):
							sort($capaFiles);
							?>	
							<div class="panel panel-warning">
								<div class="panel-heading" role="tab" id="heading<?php echo $i?>">
								  <h4 class="panel-title">
									<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $i?>" aria-expanded="false" aria-controls="collapse<?php echo $i?>">
									  <?php echo $fileDoc;?>
									</a>
								  </h4>
								</div>
								<div id="collapse<?php echo $i?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $i?>">
								  <div class="panel-body">
									<?php
									for ($j=0;$j<$qtdFiles;$j++):
										$fileDoc = $capaFiles[$j];
										$soundFile = substr($fileDoc,0,strlen($fileDoc)-4);
										$linkPDF = $capa.$dirRepertorio[$i].'/'.$fileDoc;
										$linkMP3 = $capa.$dirRepertorio[$i].'/'.$soundFile.'.mp3';
										$desc = str_replace('_', ' ', $soundFile);
										?>
										<li>
											<a href="<?php echo $linkPDF;?>" target="_new"><img src="<?php echo $capa_img?>icone_pdf.png" width="16" height="16" title="PDF"></a>
											<a href="<?php echo $linkMP3;?>" target="_new"><img src="<?php echo $capa_img?>icone_mp3.png" width="16" height="16" title="MP3"></a>
											<span><?php echo $desc;?></span>
										</li>
										<?php
									endfor;
									?>
								  </div>
								</div>
							</div>
							<?php
						endif;
					endif;
				endfor;
			endif;?>
			</div>
			</div>
			</div>
			<?php
		endif;
	endif;
}

function fGetClassTipoEvento($strTipoEvento){
	$eventClass = array(
		"APS"		=> "fa-building",	//VERMELHO
		"IASD"		=> "fa-institution",	//AZUL
		"REGIAO"	=> "fa-building-o",	//VERDE
		"DEFAULT"	=> "fa-child",	//PRETO
		"EGW"		=> "fa-graduation-cap",	//AMARELO
		"SPECIAL"	=> "fa-exclamation-triangle"	//VINHO
	);
	if (array_key_exists($strTipoEvento,$eventClass)):
		return $eventClass[$strTipoEvento];
	else:
		return fGetClassTipoEvento("DEFAULT");
	endif;
}

function getMacroArea( $tpItem, $areaInterno ){
	if ($tpItem == "CL"):
		return "CLASSE $areaInterno";
	elseif ($tpItem == "ES"):
		if ($areaInterno == "ME"):
			return "MESTRADO";
		else:
			return "ESPECIALIDADE";
		endif;
	endif;
	return $tpItem;
}

function getIconAprendizado( $tpItem, $areaInterno, $sizeClass = "" ){
    $retorno = "fa fa-info";
	if ($tpItem == "CL" && $areaInterno == "REGULAR"):
		$retorno = "fa fa-check-square";
	elseif ($tpItem == "CL" && fStrStartWith($areaInterno, "AVAN")):
		$retorno = "fa fa-check-square-o";
	elseif ($tpItem == "ES"):
		if ($areaInterno == "ME" ):
			$retorno = "fa fa-check-circle";
		else:
			$retorno = "fa fa-check-circle-o";
		endif;
	elseif ($tpItem == "TRUNFO"):
		$retorno = "fa fa-picture-o";
	elseif ($tpItem == "MEDALHA"):
		$retorno = "fa fa-trophy";
	elseif ($tpItem == "TIRA"):
		$retorno = "fa fa-square-o";
	endif;
	if (!empty($sizeClass)):
		$retorno = "$retorno $sizeClass";
	endif;
	return $retorno;
}

function fItemAprendizado($aP) {
	if (!isset($aP["classSize"])):
		$aP["classSize"] = "col-md-6 col-xs-12 col-sm-6 col-lg-4 col-xl-3";
	endif;
	$str = "<div class=\"".$aP["classSize"]."\"". (isset($aP["hint"]) ? " title=\"".$aP["hint"]."\"" : "") .">";
	$str .= "<div class=\"panel ".$aP["classPanel"]."\"";
	if ( isset($aP["fields"]) ):
		foreach ($aP["fields"] as $k => $i):
			$str .= " $k=\"$i\"";
		endforeach;
	endif;
	$str .= "><div class=\"panel-heading\"";
	$style = (isset($aP["style"]) && !is_null($aP["style"]) ? $aP["style"] : "");
	if ( isset($aP["fields"]) ):
		$style .= (empty($style) ? "" : ";")."cursor:pointer";
	endif;
	$str .= (empty($style) ? "" : "style=\"$style\"") . "><div class=\"row\">
				<div class=\"col-xs-3\"><i class=\"".$aP["leftIcon"]."\"></i></div>
				<div class=\"col-xs-9 text-right\">
					<div class=\"huge\">".$aP["value"]."</div>
				</div>
				<div class=\"col-xs-12 text-right\">".$aP["title"]."</div>
			</div>";
	$str .= "</div>";
	if ( isset($aP["fields"]) ):
		$str .= "<div id=\"detalhes\" class=\"panel-body panel-collapse collapse\"></div>";
	endif;
	if ( isset($aP["strBL"]) || isset($aP["strBR"]) ):
	$str .= "<div class=\"panel-footer\">
				<span class=\"pull-left\">".$aP["strBL"]."</span>
				<span class=\"pull-right\">".$aP["strBR"]."</span>
				<div class=\"clearfix\"></div>
			</div>";
    endif;
    $str .= "</div></div>";
    return $str;
}

function getFormsTipo(){
	$arr = array();
	$arr[] = array("id"	=> "1",	"fi" => "S", "qt" => "20", "ds"=> "20 ETIQUETAS (02x10 - 25,4mm X 101,6mm - CARTA)" );
	$arr[] = array("id"	=> "2",	"fi" => "S", "qt" => "4",  "ds"=> "04 ETIQUETAS (02x02 - 138,11mm X 106,36mm - CARTA)");
	$arr[] = array("id"	=> "3",	"fi" => "N", "qt" => "1",  "ds"=> "FOLHAS A4" );
	return $arr;
}

function fDomain($a){
	$arr = array();
	$id = "";
	$ds = "";

	$query = "SELECT ";
	if ( isset( $a['id'] ) ):
		$id = $a['id'];
		$query .= $a['id'];
	endif;
	if ( isset( $a['ds'] ) ):
		$ds = $a['ds'];
		if ( isset( $a['id'] ) ):
			$query .= ", ";
		endif;
		$query .= $a['ds'];
	else:
		$ds = $a['id'];
	endif;
	$query .= " FROM ".$a['table'];
	$query .= isset( $a['where'] ) ? " WHERE ".$a['where'] : "";
	$query .= isset( $a['order'] ) ? " ORDER BY ".$a['order'] : "";
	$dom = $GLOBALS['conn']->Execute($query);
	while (!$dom->EOF):
		$arr[] = array("value" => $dom->fields[$id], "label" => $dom->fields[$ds]);
		$dom->MoveNext();
	endwhile;
	return $arr;
}

function fDomainStatic($a,$lWrite = true){
	$arr = fDomain($a);
	if ( $lWrite ):
		$strDomain = "<option value=\"\">(NENHUM)</option>";
		foreach ($arr as $key => $value):
			$strDomain .= "<option value=\"".$value["value"]."\">".$value["label"]."</option>";
		endforeach;
		echo $strDomain;
	endif;
}

function fIdadeAtual($dData1){
	return datediff("yyyy", $dData1, date('Y-m-d'));
}

function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
    /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
        (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
    */
    
    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto = strtotime($dateto, 0);
    }
    $difference = $dateto - $datefrom; // Difference in seconds
     
    switch($interval) {
     
    case 'yyyy': // Number of full years
        $years_difference = floor($difference / 31536000);
        if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
            $years_difference--;
        }
        if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
            $years_difference++;
        }
        $datediff = $years_difference;
        break;
    case "q": // Number of full quarters
        $quarters_difference = floor($difference / 8035200);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $quarters_difference--;
        $datediff = $quarters_difference;
        break;
    case "m": // Number of full months
        $months_difference = floor($difference / 2678400);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $months_difference--;
        $datediff = $months_difference;
        break;
    case 'y': // Difference between day numbers
        $datediff = date("z", $dateto) - date("z", $datefrom);
        break;
    case "d": // Number of full days
        $datediff = floor($difference / 86400);
        break;
    case "w": // Number of full weekdays
        $days_difference = floor($difference / 86400);
        $weeks_difference = floor($days_difference / 7); // Complete weeks
        $first_day = date("w", $datefrom);
        $days_remainder = floor($days_difference % 7);
        $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
        if ($odd_days > 7) { // Sunday
            $days_remainder--;
        }
        if ($odd_days > 6) { // Saturday
            $days_remainder--;
        }
        $datediff = ($weeks_difference * 5) + $days_remainder;
        break;
    case "ww": // Number of full weeks
        $datediff = floor($difference / 604800);
        break;
    case "h": // Number of full hours
        $datediff = floor($difference / 3600);
        break;
    case "n": // Number of full minutes
        $datediff = floor($difference / 60);
        break;
    default: // Number of full seconds (default)
        $datediff = $difference;
        break;
    }    
    return $datediff;
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

function array_msort($array, $cols){
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;
}

function titleCase($string, 
			$delimiters = array(" ", "-", ".", "'", "O'", "Mc"), 
			$exceptions = array("a", "e", "da", "de", "do", "na", "no", "em", "das", "dos", "ao", "aos", "com", "I", "II", "III", "IV", "V", "VI") ){
	$string = mb_convert_case(($string), MB_CASE_TITLE, "UTF-8");
	foreach ($delimiters as $dlnr => $delimiter):
		$words = explode($delimiter, $string);
		$newwords = array();
		foreach ($words as $wordnr => $word):
			if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)):
				$word = mb_strtoupper($word, "UTF-8");
			elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)):
				$word = mb_strtolower($word, "UTF-8");
			elseif (!in_array($word, $exceptions)):
				$word = ucfirst($word);
			endif;
			array_push($newwords, $word);
		endforeach;
		$string = join($delimiter, $newwords);
   endforeach;
   return $string;
}

function arrayToObject($d) {
	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return (object) array_map(__FUNCTION__, $d);
	}
	else {
		// Return object
		return $d;
	}
}

function fArrayStr($d){
	return("'".$d."'");
}

function fStrStartWith($str,$s){
	return (strpos($str, $s, 0) === 0);
}

function fAbrevia($nome) {
	$nome = explode(" ", $nome);
	$num = count($nome);

	if ($num == 2) {
		return $nome;
	} else {
		$count = 0;
		$novo_nome = '';
		foreach ($nome as $var){
			if ($count == 0) {
				$novo_nome .= $var.' ';
			}
			$count++;
			if (count($var) > 1){
				if (($count >= 2) && ($count < $num)) {
					$array = array('da', 'de', 'di', 'do', 'das', 'dos');
					if (!in_array(strtolower($var), $array) ) {
						$novo_nome .= substr($var, 0, 1).'. ';
					}
				}
			}
			if ($count > 1 && $count == $num){
				$novo_nome .= $var;
			}
		}
		return $novo_nome;
	}
}

function fDescHora($dtHora){
	$time = strtotime($dtHora);
	$cHor = strftime("%H",$time);
	$cMin = strftime("%M",$time);
	$cRetorno = "";
	if ($cHor == "00"):
		if ($cMin > "00"):
			$cRetorno = $cHor. "h" . $cMin;
		endif;
	elseif ($cHor > "00"):
		$cRetorno = $cHor . "h";
		if ($cMin > "00"):
			$cRetorno = $cRetorno . $cMin;
		endif;
	endif;
	return $cRetorno;
}

function fDtHoraEvento($dhI, $dhF, $fmt = "%d de %B"){
	
	$D1 = date("Y-m-d", strtotime("+1 day"));
	$NOW = strtotime("now");
	$DHOJE = date("Y-m-d",$NOW);
	$HHOJE = date("H:i",$NOW);
	
	$timeI = strtotime($dhI);
	$timeF = strtotime($dhF);
	
	$DATA_EVENTO_INI = date("Y-m-d",$timeI);
	$HORA_EVENTO_INI = date("H:i",$timeI);
	$DATA_EVENTO_FIM = date("Y-m-d",$timeF);
	$HORA_EVENTO_FIM = date("H:i",$timeF);

	$sDataHora = "";

	//******************************************************************
	// SE TIVER SE DATA INICIO
	// SE DATA INICIO E FIM SAO IGUAIS
	//******************************************************************
	if (empty($dhF) || $DATA_EVENTO_INI == $DATA_EVENTO_FIM):
		if ($DATA_EVENTO_INI == $DHOJE):
			$sDataHora = "Hoje";
		elseif ($DATA_EVENTO_INI == $D1):
			$sDataHora = "Amanh&atilde;";
		else:
			$dif = datediff("d",$DHOJE,$DATA_EVENTO_INI);
			if ($dif > 0 && $dif <= 7):
				$DIA_SEMANA = strftime("%w",$timeI);
				if ($DIA_SEMANA == 0 || $DIA_SEMANA == 6):
					$sDataHora .= "Pr&oacute;ximo ";
				else:
					$sDataHora .= "Pr&oacute;xima ";
				endif;
				$sDataHora .= strftime("%A",$timeI);
			else:
				$sDataHora .= strftime($fmt,$timeI);
			endif;
		endif;
	
	//******************************************************************
	// SE DATAS INICIO E FIM SAO DIFERENTES
	//******************************************************************
	elseif ($DHOJE >= $DATA_EVENTO_INI && $DHOJE <= $DATA_EVENTO_FIM):
		if ($HHOJE <= $HORA_EVENTO_INI || $HHOJE <= $HORA_EVENTO_FIM):
			$sDataHora .= "Hoje";
		elseif ($HHOJE >= $HORA_EVENTO_FIM):
			$sDataHora .= "Amanh&aacute;";
		endif;
	else:
		//Dentro do mes
		if (strftime("%m",$timeI) == strftime("%m",$timeF)):
			$sDataHora .= strftime("%d",$timeI);
		else:
			$sDataHora .= strftime($fmt,$timeI);
		endif;
		//se dia consecutivo
		if ($D1 == $DATA_EVENTO_FIM):
			$sDataHora .= " e ";
		else:
			$sDataHora .= " a ";
		endif;
		$sDataHora .= strftime($fmt,$timeF);
	endif;

	//******************************************************************
	// SE O HORARIO FOR DIFERENTE ENTRE AS DATAS
	//******************************************************************
	if ($HORA_EVENTO_INI != $HORA_EVENTO_FIM && empty($DATA_EVENTO_FIM) && empty($HORA_EVENTO_FIM)):
		if ($DHOJE >= $DATA_EVENTO_INI && $DHOJE <= $DATA_EVENTO_FIM && $HHOJE >= $HORA_EVENTO_INI && $HHOJE <= $HORA_EVENTO_FIM):
			$sDataHora .= " at&eacute; ";
		else:
			$sDataHora = fConcatNoEmpty($sDataHora, " das ", fDescHora($dhI));
		endif;
		$sDataHora = fConcatNoEmpty($sDataHora, " &agrave;s ", fDescHora($dhF));
	else:
		$sDataHora = fConcatNoEmpty($sDataHora, " &agrave;s ", fDescHora($dhI));
	endif;
	return utf8_encode($sDataHora);
}
?>