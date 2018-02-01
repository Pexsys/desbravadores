<?php
@require_once('../include/functions.php');

class ETIQUETAS extends TCPDF {

	private $stLine;
	private $stLine2;
	private $pag;
	private $seq;
	private $maxFolha;
	private $colFolha;
	private $mTop;
	private $mLeft;
	private $uW;
	private $wH;
	private $fmtCurr;

	//
	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, 'mm', array(221, 279.4), true, 'UTF-8', false);

		$this->pag = 0;
		$this->seq = 0;

		$this->stLine = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => '',
		    'border' => false,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 7,
		    'stretchtext' => 0
		);
		$this->stLine2 = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => '',
		    'border' => false,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 10,
		    'stretchtext' => 0
		);

		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetTitle('Geração automática de identificação');
		$this->SetSubject(PATTERNS::getClubeDS(array("cl","nm")));
		$this->SetKeywords('Etiquetas, ' . str_replace(" ", ", ", PATTERNS::getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$this->SetMargins(0, 0, 0);
		$this->SetHeaderMargin(0);
		$this->SetFooterMargin(0);
		$this->SetAutoPageBreak(true, 0);
	}

 	public function Header() {
	}

	public function Footer() {
	}

	public function download() {
		$this->lastPage();
		$this->Output("Etiquetas_".date('Y-m-d_H:i:s').".pdf", 'I');
	}

	public function mapPixels() {
		$this->AddPage();
		$this->SetFont(PDF_FONT_NAME_MAIN, '', 5);
		for ($y=1;$y<=296;$y++):
			$this->SetY($y);
			$sy = ".";
			if ( (($y % 10) == 0)):
				$sy = "y$y";
				$this->Cell(0, 0, "$sy", 0, false, 'L', false, false, false, false, 'L', 'L');
			endif;
			for ($x=1;$x<=208;$x++):
				$this->SetX($x);
				$sx = ".";
				if ( (($x % 10) == 0)):
					$sx = "x$x";
					$this->Cell(0, 0, "$sx", 0, false, 'L', false, false, false, false, 'L', 'L');
				endif;
			endfor;
		endfor;
	}

	public function setFormato($nTp){
		if ($nTp == 1):
			$this->maxFolha = 20;
			$this->colFolha = 2;
			$this->mTop = 22;
			$this->mLeft = 6;
			$this->uW = 107;
			$this->uH = 25.3;

		elseif ($nTp == 2):
			$this->maxFolha = 4;
			$this->colFolha = 2;
			$this->mTop = 15;
			$this->mLeft = 0;
			$this->uW = 140;
			$this->uH = 105;

		elseif ($nTp == 3):
			$this->maxFolha = 1;
			$this->colFolha = 1;
			$this->mTop = 20;
			$this->mLeft = 0;
			$this->uW = 150;
			$this->uH = 150;
		endif;

		if ( $this->fmtCurr != $nTp) {
			$this->seq = $this->maxFolha + 1;
			$this->fmtCurr = $nTp;
		}
	}

	public function getNext($aPage){
		$this->seq++;

		//SE ULTRAPASSOU A QUANTIDADE POR FOLHA
		if ($this->seq > $this->maxFolha):
			$this->seq = 1;
		endif;

		//VERIFICA SE MUDOU DE PAGINA
		if ($this->seq == 1 || $this->pag == 0):
			 $this->SetMargins(0, 0, 0, true);
			$this->pag++;
			if ($this->fmtCurr == 3):
				$this->AddPage("P","A4");
			elseif ($this->fmtCurr == 2):
				$this->AddPage("L", array(221, 279.4) );
			else:
				$this->AddPage("P", array(221, 279.4) );
			endif;
		endif;

		if ($this->fmtCurr == 1 && $this->pag > 0):
			$this->mTop = 19;
		endif;

		if (count($aPage) > 0):
			if (isset($aPage[$this->pag-1])):
				if ($aPage[$this->pag-1]{$this->seq-1} != "S"):
					$this->getNext($aPage);
					return;
				endif;
			endif;
		endif;
	}

	public function addEtiqueta($ln) {
		$colorR = base_convert(substr($ln["CD_COR"],1,2),16,10);
		$colorG = base_convert(substr($ln["CD_COR"],3,2),16,10);
		$colorB = base_convert(substr($ln["CD_COR"],5,2),16,10);

		$yBase = ceil( $this->seq / $this->colFolha );
		$xBase = floor( $this->seq / $yBase );

		$x = (($xBase-1)*$this->uW) + $this->mLeft;
		$y = (($yBase-1)*$this->uH) + $this->mTop;

		//TIPO 1 - PASTA DE AVALIACAO
		if ($ln["TP"] == "1"):

			$this->SetLineStyle(
				array(	'width' => 0.1,
						'cap' => 'square', // butt, round, square
						'join' => 'bevel', //miter, round, bevel
						'dash' => 0,
						'color' => array($colorR, $colorG, $colorB)
				)
			);

			$this->setXY($x+30,$y+50);
			$this->SetFillColor(255,255,255);
			$this->Cell(160, 120, "", 1, false, 'C', false, false, false, false, 'T', 'M');

			$s = "img/aprendizado/PA/logotipo_".fStrZero($ln["ID_TAB_APREND"],2).".jpg";
			$this->Image($s, $x+35, $y+55, 150, 110, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 16);
			$this->SetFillColor(255,255,255);
			$this->SetTextColor($colorR, $colorG, $colorB);
			$this->setXY($x+30,$y+180);
			$this->Cell(160, 35, $ln["NM"], 1, false, 'C', false, false, false, false, 'T', 'M');

		//TIPO 2 - CAPA DA LEITURA BIBLICA
		elseif ($ln["TP"] == "2"):

			$this->SetLineStyle(
				array(	'width' => 0.1,
						'cap' => 'square', // butt, round, square
						'join' => 'bevel', //miter, round, bevel
						'dash' => 0,
						'color' => array($colorR, $colorG, $colorB)
				)
			);

			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 16);
			$this->SetFillColor(255,255,255);
			$this->SetTextColor($colorR, $colorG, $colorB);

			$s = "img/aprendizado/PA/logotipo_".fStrZero($ln["ID_TAB_APREND"],2).".jpg";
			$this->Image($s, $x+30, $y+20, 40, 30, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			$this->setXY($x+72,$y+20);
			$this->Cell(118, 30, "RELATÓRIO DE LEITURA BÍBLICA", 1, false, 'C', false, false, false, false, 'T', 'M');

			$this->setXY($x+30,$y+52);
			$this->SetFillColor(255,255,255);
			$this->Cell(160, 120, "", 1, false, 'C', false, false, false, false, 'T', 'M');

			$s = "img/aprendizado/LB/image1.jpg";
			$this->Image($s, $x+35, $y+57, 150, 110, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			$this->setXY($x+30,$y+174);
			$this->Cell(160, 35, $ln["NM"], 1, false, 'C', false, false, false, false, 'T', 'M');

		//TIPO 0 - MEMBROS DO CLUBE
		elseif ($ln["TP"] == "0"):
			$this->setXY($x,$y);
			$this->Image("img/logo.jpg", $x+2, $y-5, 15, 16, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
			$this->SetFillColor(255,255,255);
			$this->SetTextColor(0,0,0);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
			$this->setXY($x+18,$y-4);
			$this->MultiCell(82, 8, $ln["NM"], false, 'C', false, 2, "", "", true, 0, false, true, 0, "M", false );

			//CASO TENHA ITEM DE CLASSE PARA IMPRIMIR
			if (!is_null($ln["ID_TAB_APREND"])):
				$this->setXY($x+34,$y+8);
				$this->SetFillColor($colorR, $colorG, $colorB);
				if ($ln["ID_TAB_APREND"] == 11 || $ln["ID_TAB_APREND"] == 12):
					$this->SetTextColor(0,0,0);
				else:
					$this->SetTextColor(255,255,255);
				endif;
				$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 6);
				$this->MultiCell(50, 3, $ln["DS_ITEM"], false, 'C', true, 0, "", "", true, 0, false, true, 0, "M", false );
			endif;

		//TIPO C - PASTA DE CLASSE
		elseif ($ln["TP"] == "C"):
			$this->Image("img/logo.jpg", $x+8, $y-5, 20, 22, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			$this->setXY($x+34,$y);
			$this->write1DBarcode($ln["BC"], 'C39', '', '', '', 20, 0.45, $this->stLine2, 'N');

			$this->Image("img/regiao.jpg", $x+112, $y-6, 20, 23, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
			$this->setXY($x+2,$y+32);
			$this->SetTextColor($colorR, $colorG, $colorB);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 21);
			$this->MultiCell(126, 25, $ln["DS_ITEM"], false, 'C', false, 2, "", "", true, 0, false, true, 0, "M", false );

			$this->setXY($x+2,$y+75);
			$this->SetTextColor(0,0,0);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 17);
			$this->Cell(126, 25, $ln["NM"], false, false, 'C', false, false, true, false, 'B', 'M');

			$this->setXY($x+2,$y+83);
			$this->SetTextColor(180,180,180);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 7);
			$this->MultiCell(126, 6,
				PATTERNS::getClubeDS( array("cl","nm") ) ." - desde ".
				PATTERNS::getClubeDS( array("af") )."\n".
				PATTERNS::getClubeDS( array("ibd","rg","ab","un","dv","sp") ), false, 'C', false, 2, "", "", true, 0, false, true, 0, "M", false );

		//TIPO E - CARTAO DE ESPECIALIDADE
		elseif ($ln["TP"] == "E"):
			$this->setXY($x,$y);
			$this->Image("img/logo.jpg", $x+2, $y-5, 15, 16, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
			$this->SetFillColor(255,255,255);
			$this->SetTextColor(0,0,0);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
			$this->setXY($x+18,$y+1);
			$this->Cell(78, 7, $ln["NM"], false, false, 'C', false, false, true, false, 'B', 'M');

			$this->setXY($x+28,$y+1);
			$this->write1DBarcode($ln["BC"], 'C39', '', '', '', 15, 0.35, $this->stLine, 'N');

		//OUTROS TIPOS
		else:
			$this->setXY($x,$y);
			$this->Image("img/logo.jpg", $x+2, $y-5, 15, 16, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

			//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
			$this->SetFillColor(255,255,255);
			$this->SetTextColor(0,0,0);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
			$this->setXY($x+18,$y+1);
			$this->Cell(78, 8, $ln["NM"], false, false, 'C', false, false, true, false, 'B', 'M');

			if (strpos("AB", $ln["TP"]) !== FALSE ):
				$this->setXY($x+40,$y+3.5);
				$this->write1DBarcode($ln["BC"], 'C39', '', '', '', 15, 0.35, $this->stLine, 'N');

				$this->setXY($x+2,$y+15);
				$this->SetFillColor($colorR, $colorG, $colorB);
				if ($ln["ID_TAB_APREND"] > 10):
					$this->SetTextColor(0,0,0);
				else:
					$this->SetTextColor(255,255,255);
				endif;
				$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 5);
				$this->Cell(35, 3, $ln["DS_ITEM"], 0, false, 'C', true, false, false, false, 'B', 'M');
			endif;
		endif;
	}
}

$where = "";
$id = fRequest("id");
if (isset($id) && !empty($id)):
	$where .= " AND pt.ID IN ($id)";
endif;

$ip = fRequest("ip");
if (isset($ip) && !empty($ip)):
	$where .= " AND pt.ID_CAD_MEMBRO IN ($ip)";
endif;

$query = "
	SELECT DISTINCT pt.MD
	  FROM TMP_PRINT_TAGS pt
	 WHERE pt.MD = ?
	   $where
	 ORDER BY 1
";
$arr = array( fRequest("md") );
$result = CONN::get()->Execute($query, $arr);
if ($result->EOF):
	echo "Fila de impress&atilde;o de identifica&ccedil;&atilde;o n&atilde;o encontrada para esta sele&ccedil;&atilde;o!";
	exit;
endif;

$pg = fRequest("pg");

$aPageParam = array();
if (isset($pg) && !empty($pg)):
	$aPageParam = explode(",",$pg);
endif;

$pdf = new ETIQUETAS();

foreach ($result as $k => $l):

	$aPage = array();
	$pdf->setFormato($l["MD"]);
	if ($l["MD"] != 3):
		$aPage = $aPageParam;
	endif;

	$rs = CONN::get()->Execute("
		SELECT DISTINCT
				pt.TP,
				pt.BC,
				pt.ID_TAB_APREND,
				at.NM,
				ap.CD_COR,
				ap.DS_ITEM
		FROM TMP_PRINT_TAGS pt
		INNER JOIN CON_ATIVOS at ON (at.ID_CAD_MEMBRO = pt.ID_CAD_MEMBRO)
		 LEFT JOIN TAB_APRENDIZADO ap ON (ap.ID = pt.ID_TAB_APREND)
		WHERE pt.MD = ?
		  $where
	 ORDER BY pt.BC, pt.ID_TAB_APREND
	", array($l["MD"]) );
	foreach ($rs as $ks => $ls):
		$pdf->getNext($aPage);
		$pdf->addEtiqueta($ls);
	endforeach;

endforeach;

$pdf->download();
exit;
?>
