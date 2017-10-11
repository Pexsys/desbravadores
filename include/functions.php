<?php
ini_set('memory_limit','200M');
error_reporting (E_ALL & ~ E_NOTICE & ~ E_DEPRECATED); //
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');

global $conn, $DBType, $DBServerHost, $DBUser, $DBPassWord, $DBDataBase, $DBRegras, $VirtualDir;
@require_once("_variables.php");
@require_once("_core/lib/adodb5/adodb.inc.php");
@require_once("_core/lib/dbconnect/_base.php");

function fClubeID() {
    return "Clube de Desbravadores Pioneiros - IASD Capão Redondo - 6ª Região - APS - UCB - DSA";
}

function fGetPerfil( $cd = NULL ) {
	$arr = array();
	$query = "SELECT DISTINCT td.id, td.cd, td.iconm, td.iconf, td.ds_menu, tf.ds_url
		    FROM CAD_USU_PERFIL cpp
	      INNER JOIN TAB_PERFIL_ITEM tpi ON ( tpi.id_tab_perfil = cpp.id_perfil ) 
	      INNER JOIN TAB_DASHBOARD td ON ( td.id = tpi.id_tab_dashboard ) 
	       LEFT JOIN TAB_FUNCTION tf ON ( tf.id = td.id_tab_function ) 
		   WHERE cpp.id_cad_usuarios = ?";
	if ( isset($cd) && !empty($cd) ):
		$query .= " AND td.cd LIKE '$cd.%' AND LENGTH(td.cd) = LENGTH('$cd')+3";
	else:
		$query .= " AND LENGTH(td.cd) = 2";
	endif;
	$query .= " ORDER BY td.cd";
	$result = $GLOBALS['conn']->Execute($query, array($_SESSION['USER']['id_usuario']) );
	while (!$result->EOF):
		$child = fGetPerfil( $result->fields['cd'] );
		$arr[ $result->fields['id'] ] = array( 
			"opt"	 => ($result->fields['ds_menu']),
			"ico"	 => ( $_SESSION['USER']['sexo'] == "F" && isset($result->fields['iconf']) ? $result->fields['iconf'] :  $result->fields['iconm'] ),
			"active" => false
		);
		if ( count( $child ) > 0 ):
			$arr[ $result->fields['id'] ]["child"] = $child;
		else:
			$arr[ $result->fields['id'] ]["url"] = $result->fields['ds_url'];
		endif;
		$result->MoveNext();
	endwhile;
	return $arr;
}

function verificaPerfil(){
	$temPerfil = isset($_SESSION['USER']['ssid']);
	if (!$temPerfil):
		session_destroy();
		header("Location: ".$GLOBALS['VirtualDir']."index.php");
		exit;
	endif;
}

function fSetSessionLogin( $result ){
	session_start();
	$_SESSION['USER']['ssid']			= session_id();
	$_SESSION['USER']['cd_usuario']		= $result->fields['CD_USUARIO'];
	$_SESSION['USER']['ds_usuario']		= $result->fields['DS_USUARIO'];
	$_SESSION['USER']['id_usuario']		= $result->fields['ID_USUARIO'];
	$_SESSION['USER']['id_cad_pessoa']	= $result->fields['ID_CAD_PESSOA'];
	$_SESSION['USER']['sexo']			= (!is_null($result->fields['TP_SEXO_RESP']) ? $result->fields['TP_SEXO_RESP'] : $result->fields['TP_SEXO']);
}

function responseMethod() {
    header('Content-type: application/json');
	// Getting the json data from the request
	$response = '';
	
	$json_data = json_decode( json_encode( $_POST ) );
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
<html>
<head>
<title>Clube Pioneiros - IASD Capão Redondo</title>
<?php @require_once("_metaheader.php");?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/css/modern-business.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $GLOBALS['VirtualDir'];?>include/_core/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<?php 
if (isset($aCssFiles)):
	foreach ($aCssFiles as &$file):
	?><link href="<?php echo $file;?>" rel="stylesheet" type="text/css"><?php 
	endforeach;
endif;
?>
<script type="text/javascript" src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/formValidation/formValidation.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/formValidation/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['VirtualDir'];?>include/_core/js/bootstrap-dialog.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['VirtualDir'];?>js/functions.lib.js"></script>
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

/*
REGRAS MESTRADO

  let masterRules = {
    
    //ADRA - 509
    ME001 : [
      {  //1
        min : 6,
        selection : [ 
          'AD001', 'AD002', 'AD003', 'AD004', 'AD005', 'AD006', 'AD007', 'AD008', 'AD009'
        ]
      },
      {  //2
        min : 1,
        selection : [ 
          'AM004', 'AM007', 'AM010', 'AM013', 'HM029' 
        ]
      }
    ],

    //ARTES A HABILIDADES MANUAIS - 510
    ME002 : [
      {  //3
        min : 7,
        selection : [ 
          'HM001', 'HM002', 'HM003', 'HM004', 'HM005', 'HM006', 'HM007', 'HM008', 'HM009', 'HM010',
          'HM011', 'HM012', 'HM013', 'HM014', 'HM015', 'HM016', 'HM017', 'HM018', 'HM019', 'HM020',
          'HM021', 'HM022', 'HM023', 'HM024', 'HM025', 'HM026', 'HM027', 'HM028', 'HM029', 'HM030',
          'HM031', 'HM032', 'HM033', 'HM034', 'HM035', 'HM036', 'HM037', 'HM038', 'HM039', 'HM040',
          'HM041', 'HM042', 'HM043', 'HM044', 'HM045', 'HM046', 'HM047', 'HM048', 'HM049', 'HM050',
          'HM051', 'HM052', 'HM053', 'HM054', 'HM055', 'HM056', 'HM057', 'HM058', 'HM059', 'HM060',
          'HM061', 'HM062', 'HM063', 'HM064', 'HM065', 'HM066', 'HM067', 'HM068', 'HM069', 'HM070',
          'HM071', 'HM072', 'HM073', 'HM074', 'HM075', 'HM076', 'HM077', 'HM078', 'HM079', 'HM080',
          'HM081', 'HM082', 'HM083', 'HM084', 'HM085', 'HM086', 'HM087', 'HM088'
        ]
      }
    ],
    
    //ATIVIDADES AGRICOLAS - 511
    ME003 : [
      {  //4
        min : 7,
        selection : [ 
          'AA001', 'AA002', 'AA003', 'AA004', 'AA005', 'AA006', 'AA007', 'AA008', 'AA009', 'AA010', 
          'AA011', 'AA012', 'AA013', 'AA014', 'AA015'
        ]
      }
    ],
    
    //TESTIFICACAO - 512
    ME004 : [
      {  //5
        min : 7,
        selection : [ 
          'AM001', 'AM002', 'AM003', 'AM004', 'AM005', 'AM006', 'AM008', 'AM009', 'AM010', 'AM011', 
          'AM012', 'AM013', 'AM014', 'AM015', 'AM016', 'AM017', 'AM018', 'AM019', 'AM020', 'AM021', 
          'AM022', 'AM023', 'AM024', 'AM029', 'AM030', 'AM031', 'AM032', 'AM034', 'AM035', 'AM036', 
          'AM037', 'AM038', 'AM039', 'AM040', 'AM041', 'AM042', 'AM043'
        ]
      }
    ],
    
    //PROFISSIONAIS - 513
    ME005 : [
      {  //6
        min : 7,
        selection : [ 
          'AP001', 'AP002', 'AP003', 'AP004', 'AP005', 'AP006', 'AP007', 'AP008', 'AP009', 'AP010', 
          'AP011', 'AP012', 'AP013', 'AP014', 'AP015', 'AP016', 'AP017', 'AP018', 'AP019', 'AP020', 
          'AP021', 'AP022', 'AP023', 'AP024', 'AP025', 'AP026', 'AP027', 'AP028', 'AP029', 'AP031', 
          'AP033', 'AP032', 'AP036', 'AP037', 'AP038', 'AP040', 'AP046', 'AP047', 'AP048', 'AP050', 
          'AP051', 'AP053', 'AP054', 'AP055', 'AP056', 'AP058', 'AP059', 'AP060', 'AP061', 'AP062'
        ]
      }
    ],
    
    //CIENCIA E TECNOLOGIA - 514
    ME006 : [
      {  //7
        min : 7,
        selection : [ 
          'CS002', 'CS007', 'CS013', 'CS021', 'CS022', 'AP034', 'AP035', 'AP041', 'AP042', 'AP043', 
          'AP044', 'AP045', 'AP049', 'AP052'
        ]
      }
    ],
    
    //AQUATICA - 515
    ME007 : [
      {  //8
        min : 7,
        selection : [ 
          'AR005', 'AR008', 'AR015', 'AR016', 'AR018', 'AR019', 'AR023', 'AR026', 'AR027', 'AR028', 
          'AR029', 'AR030', 'AR031', 'AR032', 'AR039', 'AR079', 'AR061', 'AR105'
        ]
      }
    ],
    
    //ESPORTES - 516
    ME008 : [
      {  //9
        min : 7,
        selection : [ 
          'AR002', 'AR008', 'AR013', 'AR014', 'AR025', 'AR030', 'AR034', 'AR035', 'AR037', 'AR038', 
          'AR041', 'AR042', 'AR043', 'AR044', 'AR049', 'AR054', 'AR060', 'AR063', 'AR065', 'AR066', 
          'AR072', 'AR073', 'AR074', 'AR075', 'AR076', 'AR080', 'AR091', 'AR094', 'AR097', 'AR098', 
          'AR103', 'AR107', 'AR108', 'AR109'
        ]
      }
    ],
    
    //VIDA CAMPESTRE - 517
    ME009 : [
      {  //10
        min : 7,
        selection : [ 
          'AR001', 'AR010', 'AR012', 'AR020', 'AR021', 'AR022', 'AR024', 'AR033', 'AR036', 'AR045', 
          'AR046', 'AR053', 'AR056', 'AR057', 'AR058', 'AR070', 'AR085', 'AR088', 'AR089', 'AR095', 
          'AR099', 'AR101', 'AR102', 'AR104', 'EN043'
        ]
      }
    ],
    
    //ATIVIDADES RECREATIVAS - 518
    ME010 : [
      {  //11
        min : 7,
        selection : [ 
          'AR011', 'AR012', 'AR017', 'AR040', 'AR047', 'AR048', 'AR050', 'AR059', 'AR062', 'AR067', 
          'AR068', 'AR071', 'AR077', 'AR078', 'AR081', 'AR082', 'AR083', 'AR086', 'AR087', 'AR092', 
          'AR093', 'AR096', 'AR099', 'AR100', 'AR106'
        ]
      }
    ],
    
    //SAUDE - 519
    ME011 : [
      {  //12
        min : 7,
        selection : [ 
          'CS001', 'CS003', 'CS004', 'CS005', 'CS006', 'CS008', 'CS010', 'CS011', 'CS012', 'CS015', 
          'CS016', 'CS017', 'CS018', 'CS019', 'CS020', 'CS023', 'CS024', 'CS026', 'CS028', 'CS029', 
          'CS030'
        ]
      }
    ],
    
    //ZOOLOGIA - 520
    ME012 : [
      {  //13
        min : 7,
        selection : [ 
          'EN001', 'EN003', 'EN004', 'EN007', 'EN008', 'EN010', 'EN011', 'EN014', 'EN020', 'EN022', 
          'EN023', 'EN024', 'EN025', 'EN027', 'EN030', 'EN031', 'EN032', 'EN034', 'EN037', 'EN051', 
          'EN052', 'EN054', 'EN055', 'EN057', 'EN059', 'EN060', 'EN061', 'EN065', 'EN066', 'EN068', 
          'EN069', 'EN070', 'EN071', 'EN076', 'EN078', 'EN080', 'EN083', 'EN085', 'EN087', 'EN090', 
          'EN094'
        ]
      }
    ],
    
    //ECOLOGIA - 521
    ME013 : [
      {  //14
        min : 3,
        selection : [ 
          'EN044', 'EN046', 'EN058'
        ]
      },
      { //15
        min : 4,
        selection : [ 
          'EN045', 'EN067', 'EN073', 'EN081', 'EN082', 'EN089', 'EN092', 'EN093'
        ]
      }
    ],
    
    //BOTANICA - 522
    ME014 : [
      { //16
        min : 7,
        selection : [ 
          'EN005', 'EN006', 'EN015', 'EN018', 'EN019', 'EN021', 'EN029', 'EN033', 'EN036', 'EN038', 
          'EN039', 'EN040', 'EN042', 'EN048', 'EN049', 'EN053', 'EN062', 'EN063', 'EN072', 'EN074', 
          'EN077', 'EN084', 'EN086', 'EN088'
        ]
      }
    ],
    
    //HABILIDADES DOMESTICAS - 523
    ME015 : [
      { //17
        min : 7,
        selection : [ 
          'HD001', 'HD002', 'HD003', 'HD004', 'HD005', 'HD006', 'HD007', 'HD008', 'HD009', 'HD010', 
          'HD011', 'HD012'
        ]
      }
    ]
  };
*/

function zeroSizeID(){
	$rs = $GLOBALS['conn']->Execute("SELECT COUNT(*) AS qtd FROM CAD_PESSOA");
	if (!$rs->EOF):
		return strlen($rs->fields['qtd']);
	endif;
	return 5;
}

function fDescMes($cMes){
	$cRet = "";
	if ($cMes == "01"):
		$cRet = "Janeiro";
	elseif ($cMes == "02"):
		$cRet = "Fevereiro";
	elseif ($cMes == "03"):
		$cRet = "Março";
	elseif ($cMes == "04"):
		$cRet = "Abril";
	elseif ($cMes == "05"):
		$cRet = "Maio";
	elseif ($cMes == "06"):
		$cRet = "Junho";
	elseif ($cMes == "07"):
		$cRet = "Julho";
	elseif ($cMes == "08"):
		$cRet = "Agosto";
	elseif ($cMes == "09"):
		$cRet = "Setembro";
	elseif ($cMes == "10"):
		$cRet = "Outubro";
	elseif ($cMes == "11"):
		$cRet = "Novembro";
	elseif ($cMes == "12"):
		$cRet = "Dezembro";
	endif;
	return $cRet;
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

function fFormatFromDB($pValue,$pTipo){
	$retorno = $pValue;
	if ($pValue == ""):
		return "";
	endif;
	if ($GLOBALS['DBType'] == "access"):
		if ($pTipo == "D"):
			$retorno = substr($pValue,0,2) ."/". substr($pValue,3,2) ."/". substr($pValue,6,4);
		elseif ($pTipo == "DHm"):
			$retorno = substr($pValue,0,2) ."/". substr($pValue,3,2) ."/". substr($pValue,6,4) . " " . substr($pValue,11,5);
		elseif ($pTipo == "DHms"):
			$retorno = substr($pValue,0,2) ."/". substr($pValue,3,2) ."/". substr($pValue,6,4) . " " . substr($pValue,11,8);
		endif;
	else:
		if ($pTipo == "D"):
			$retorno = substr($pValue,8,2) ."/". substr($pValue,5,2) ."/". substr($pValue,0,4);
		elseif ($pTipo == "DHm"):
			$retorno = substr($pValue,8,2) ."/". substr($pValue,5,2) ."/". substr($pValue,0,4) . " " . substr($pValue,11,5);
		elseif ($pTipo == "DHms"):
			$retorno = substr($pValue,8,2) ."/". substr($pValue,5,2) ."/". substr($pValue,0,4) . " " . substr($pValue,11,8);
		elseif ($pTipo == "DM"):
			$retorno = substr($pValue,8,2) ."/". substr($pValue,5,2);
		elseif ($pTipo == "DMr"):
			$retorno = (substr($pValue,8,2)*1) ."/". (substr($pValue,5,2)*1);
		elseif ($pTipo == "MDA"):
			$retorno = substr($pValue,5,2) ."/". substr($pValue,8,2) ."/". substr($pValue,0,4);
		endif;
	endif;
	return $retorno;
}

function fDifDatas($pDataIni,$pDataFim,$pRetorno){
	$retorno = 0;

	$nAnoIni = substr($pDataIni,0,4);
	$nMesIni = substr($pDataIni,5,2);
	$nDiaIni = substr($pDataIni,8,2);
	$nHorIni = substr($pDataIni,11,2);
	$nMinIni = substr($pDataIni,14,2);
	$nSegIni = substr($pDataIni,17,2);
	$dDataIni = mktime($nHorIni,$nMinIni,$nSegIni,$nMesIni,$nDiaIni,$nAnoIni);
	if ($pDataFim == "ATU"):
		$dDataFim = time();
	else:
		$nAnoFim = substr($pDataFim,0,4);
		$nMesFim = substr($pDataFim,5,2);
		$nDiaFim = substr($pDataFim,8,2);
		$nHorFim = substr($pDataFim,11,2);
		$nMinFim = substr($pDataFim,14,2);
		$nSegFim = substr($pDataFim,17,2);
		$dDataFim = mktime($nHorFim,$nMinFim,$nSegFim,$nMesFim,$nDiaFim,$nAnoFim);
	endif;

	//retorno em dias
	if ($pRetorno == "D"):
		$retorno = (int)(($dDataFim - $dDataIni) / 86400);
	elseif ($pRetorno == "HH"):
		$retorno = (int)(($dDataFim - $dDataIni) / 3600);
	elseif ($pRetorno == "MM"):
		$retorno = (int)(($dDataFim - $dDataIni) / 60);
	elseif ($pRetorno == "SS"):
		$retorno = ($dDataFim - $dDataIni);
	endif;
	return $retorno;
}

function fMontaCarrousel($relativePath,$extentions){
	$capa = $GLOBALS['VirtualDir'] . $relativePath;

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

function fListDocumentos($relativePath,$title,$extentions,$classPanel,$tagItem){
	$capa = $GLOBALS['VirtualDir'] . $relativePath;
	$capa_img = $GLOBALS['VirtualDir'] . $relativePath."img/";
	
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
	$capa = $GLOBALS['VirtualDir'] . $relativePath;
	$capa_img = $GLOBALS['VirtualDir'] . "img/";

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
	elseif ($tpItem == "ES" && $areaInterno == "ME"):
		return "MESTRADO";
	else:
		return "ESPECIALIDADE";
	endif;
}

function consultaAprendizadoPessoa( $tabAprendID, $pessoaID ){
	$arr = array( "ap" => "", "ar" => "", "cd" => "", "nm" => "" );
	$rs = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM TAB_APRENDIZADO
		 WHERE ID = ?
	", array( $tabAprendID ) );
	if (!$rs->EOF):
		$arr["cr"] = $rs->fields["CD_COR"];
		$arr["ap"] = $rs->fields["DS_ITEM"];
		$arr["ar"] = $rs->fields["CD_AREA_INTERNO"];
		$arr["cd"] = $rs->fields["CD_ITEM_INTERNO"];
	endif;

	$rp = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CON_ATIVOS
		 WHERE ID = ?
	", array( $pessoaID ) );
	if (!$rp->EOF):
		$arr["nm"] = ($rp->fields["NM"]);
	endif;
	return $arr;
}

function getIconAprendizado( $tpItem, $areaInterno, $sizeClass = "" ){
        $retorno = "";
	if ($tpItem == "CL" && $areaInterno == "REGULAR"):
		$retorno = "fa fa-check-square";
	elseif ($tpItem == "CL" && fStrStartWith($areaInterno, "AVAN")):
		$retorno = "fa fa-check-square-o";
	elseif ( $tpItem == "ES" && $areaInterno == "ME" ):
		$retorno = "fa fa-check-circle";
	else:
		$retorno = "fa fa-check-circle-o";
	endif;
	if (!empty($sizeClass)):
		$retorno = "$retorno $sizeClass";
	endif;
	return $retorno;
}

function fItemAprendizado( $panelClass, $iconLeft, $value, $titulo, $detalhes = null, $detalhes2 = "", $style = "", $fields = null, $panelClassSize = "col-md-6 col-xs-12 col-sm-6 col-lg-4 col-xl-3" ) {
	echo "<div class=\"$panelClassSize\">";
	if ( isset($fields) ):
		echo "  <div class=\"panel $panelClass\"";
		foreach ($fields as $k => $i):
		    echo " $k=\"$i\"";
		endforeach;
		echo "><div class=\"panel-heading\"". (empty($style)?"style=\"cursor:pointer;\"":" style=\"cursor:pointer;$style\"").">";
	else:
		echo "  <div class=\"panel $panelClass\">";
		echo "	<div class=\"panel-heading\"". (empty($style)?"":" style=\"$style\"").">";
	endif;
	echo "	<div class=\"row\">
					<div class=\"col-xs-3\"><i class=\"$iconLeft\"></i></div>
					<div class=\"col-xs-9 text-right\">
						<div class=\"huge\">$value</div>
					</div>
					<div class=\"col-xs-12 text-right\">$titulo</div>
				</div>";
	echo "	</div>";
	if ( isset($fields) ):
		echo "<div id=\"detalhes\" class=\"panel-body panel-collapse collapse\"></div>";
	endif;
	if ( isset($detalhes) || isset($detalhes2) ):
    	echo "<div class=\"panel-footer\">
    				<span class=\"pull-left\">$detalhes</span>
    				<span class=\"pull-right\">$detalhes2</span>
    				<div class=\"clearfix\"></div>
    			</div>";
    endif;
    echo "</div></div>";
}

function fFormataData($data,$formato){
	$dd = "";
	$mm = "";
	$yyyy = "";
	$retorno = "";
	if(!empty($data)){	
		switch($formato){
		case "DD/MM":
			//FORMATO DE ENTRADA - YYYY-MM-DD
			$dd = substr($data,8,2);
			$mm = substr($data,5,2);
			$retorno = $dd."/".$mm;
			break;
		case "DD/MM/YYYY":
			//FORMATO DE ENTRADA - YYYY-MM-DD
			$dd = substr($data,8,2);
			$mm = substr($data,5,2);
			$yyyy = substr($data,0,4);
			$retorno = $dd."/".$mm."/".$yyyy;
			break;
		case "YYYY-MM-DD":
			//FORMATO DE ENTRADA - DD/MM/YYYY
			$dd = substr($data,0,2);
			$mm = substr($data,3,2);
			$yyyy = substr($data,6,4);
			$retorno = $dd."/".$mm."/".$yyyy;
			break;
		}
	}
	return $retorno;
}

function fStrZero($n,$q){
	return str_pad($n, $q, "0", STR_PAD_LEFT);
}

function getOptionTag($array,$option){
	$arr = array();
	foreach ($array as $k):
		if ($k["id"] == $option):
			return $k;
		endif;
	endforeach;
	return $arr;
}

function getTagsTipo(){
	$arr = array();
	$arr[] = array("id"	=> "0",	"cl" => "N", "md" => "1", "ds"=> "0-BÁSICA/NOME" );
	$arr[] = array("id"	=> "1",	"cl" => "S", "md" => "3", "ds"=> "1-CAPA DA PASTA DE AVALIAÇÃO" );
	$arr[] = array("id"	=> "2",	"cl" => "S", "md" => "3", "ds"=> "2-CAPA DE LEITURA BÍBLICA" );
	$arr[] = array("id"	=> "A",	"cl" => "S", "md" => "1", "ds"=> "A-CARTÃO DE CLASSE" );
	$arr[] = array("id"	=> "B",	"cl" => "S", "md" => "1", "ds"=> "B-CADERNO DE ATIVIDADES" );
	$arr[] = array("id"	=> "C",	"cl" => "S", "md" => "2", "ds"=> "C-PASTA DE CLASSE" );
	$arr[] = array("id"	=> "E",	"cl" => "N", "md" => "1", "ds"=> "E-CARTÃO DE ESPECIALIDADES" );
	return $arr;
}

function getFormsTipo(){
	$arr = array();
	$arr[] = array("id"	=> "1",	"fi" => "S", "qt" => "20", "ds"=> "20 ETIQUETAS (02x10 - 25,4mm X 101,6mm - CARTA)" );
	$arr[] = array("id"	=> "2",	"fi" => "S", "qt" => "4",  "ds"=> "04 ETIQUETAS (02x02 - 138,11mm X 106,36mm - CARTA)");
	$arr[] = array("id"	=> "3",	"fi" => "N", "qt" => "1",  "ds"=> "FOLHAS A4" );
	return $arr;
}

/************************************************************
* FUNCAO COLOCAR MASCARA NO CEP, CPF, CGC, DDD, TEL e PLACA
*************************************************************/
function fMascara($numero,$tipo){
	
	$retorno=""; 
	
	$numero = trim($numero);
	
	switch($tipo){
		case "CPF":
			$numero = fStrZero($numero,11);
			$retorno = substr($numero,0,3);
			$retorno = $retorno . ".";
			$retorno = $retorno . substr($numero,3,3);
			$retorno = $retorno . ".";
			$retorno = $retorno . substr($numero,6,3);
			$retorno = $retorno . "-";
			$retorno = $retorno . substr($numero,9,2);
			break;
		case "CGC":
			$numero = fStrZero($numero,14);
			$retorno = substr($numero,0,2);
			$retorno = $retorno . ".";
			$retorno = $retorno . substr($numero,2,3);
			$retorno = $retorno . ".";
			$retorno = $retorno . substr($numero,5,3);
			$retorno = $retorno . "/";
			$retorno = $retorno . substr($numero,8,4);
			$retorno = $retorno . "-";
			$retorno = $retorno . substr($numero,12,2);
			break;
		case "CEP":
			$numero = fStrZero($numero,8);
			$retorno = substr($numero,0,5);
			$retorno = $retorno . "-";
			$retorno = $retorno . substr($numero,5,3);
			break;
		case "DDD":
			$retorno = "(".$numero.")";
			break;
		case "TEL":
			$retorno = substr($numero,0,4) . "-" . substr($numero,4,4);
			break;
		case "CEL":
			if(strlen($numero) == 8){
				$retorno = substr($numero,0,4) . "-" . substr($numero,4,4);
			}else{
				$retorno = substr($numero,0,5) . "-" . substr($numero,5,4);
			}
			break;
	}
	return $retorno;
}

function fNormalizeStr($str,$charset){
	if ($charset == "ISO-8859-1"):
		$some_special_chars = array( "á", "â", "ã", "é", "ê", "í", "î", "ó", "ô", "õ", "ú", "ç", "Á", "Â", "Ã", "É", "Ê", "Í", "Î", "Ó", "Ô", "Õ", "Ú", "Ç" );
	else:
		$some_special_chars = array( "á", "â", "ã", "é", "ê", "í", "î", "ó", "ô", "õ", "ú", "ç", "Á", "Â", "Ã", "É", "Ê", "Í", "Î", "Ó", "Ô", "Õ", "Ú", "Ç" );
	endif;
	$replacement_chars  = array( "a", "a", "a", "e", "e", "i", "i", "o", "o", "o", "u", "c","A", "A", "A", "E", "E", "I", "I", "O", "O", "O", "U", "C" );
	return str_replace( $some_special_chars, $replacement_chars, $str );
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

function fCalculaAniversario($dData1, $dData2){
	$nRetorno = datediff("yyyy", $dData1, $dData2);
	if ($nRetorno > 0):
		/*
		$time1 = strtotime( $dData1 );
		$time2 = strtotime( $dData2 );
		$mes1 = date( 'm', $time1 );
		$mes2 = date( 'm', $time2 );
		$dia1 = date( 'd', $time1 );
		$dia2 = date( 'd', $time2 );
		echo "$nRetorno";
		exit;
		if ( ($mes2 < $mes1) || 
			($mes1 == $mes2 && $dia1 < $dia2) ):
			$nRetorno--;
		endif;
		*/
	endif;
	return $nRetorno;
}

function fIdadeAtual($dData1){
  return fCalculaAniversario($dData1, date("Y-m-d"));
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
?>