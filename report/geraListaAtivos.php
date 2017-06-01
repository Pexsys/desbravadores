<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAATIVOSALFA extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $posY;
	private $lineAlt;
	
	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$this->stLine = array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->stLine2 = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->stLine3 = array(
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
		    'fontsize' => 9,
		    'stretchtext' => 0
		);

		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetTitle('Listagem de Membros Ativos');
		$this->SetSubject('Clube Pioneiros');
		$this->SetKeywords('Desbravadores, Especialidades, Pioneiros, Capão Redondo');
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}

	public function Footer() {
		$this->Line(5, 288, 205, 288, $this->stLine3);
		$this->SetY(-9);
		$this->SetTextColor(90,90,90);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
		$this->SetX(5);
		$this->Cell(50, 3, date("d/m/Y H:i:s"), 0, false, 'L');
		$this->SetX(172);
		$this->Cell(40, 3, "Página ". $this->getAliasNumPage() ." de ". $this->getAliasNbPages(), 0, false, 'R');
	}
	
 	public function Header() {
		$this->setXY(0,0);
		$this->Image("img/logo.jpg", 5, 5, 14, 16, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$this->setXY(20,5);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 25);
		$this->Cell(185, 9, "Listagem de Membros Ativos", 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->setXY(20,15);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->Cell(185, 5, fClubeID(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(80,80,80);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, 22);
		$this->Cell(85, 6, "Nome Completo", 0, false, 'L', true);
		$this->setXY(90, 22);
		$this->Cell(50, 6, "Cargo", 0, false, 'L', true);
		$this->setXY(140, 22);
		$this->Cell(30, 6, "Idade/Nasc.", 0, false, 'C', true);
		$this->setXY(170, 22);
		$this->Cell(35, 6, "Telefones", 0, false, 'L', true);
		$this->posY = 28;
	}

	public function addLine($f){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->setCellPaddings(1,1,1,1);
		if ($this->lineAlt):
			$this->SetFillColor(240,240,240);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->setXY(5, $this->posY);
		$this->Cell(85, 5, utf8_encode($f["NM"]), 0, false, 'L', true, false, 1);
		$this->setX(90);

		$colorR = base_convert(substr($f["CD_COR_GENERO"],1,2),16,10);
		$colorG = base_convert(substr($f["CD_COR_GENERO"],3,2),16,10);
		$colorB = base_convert(substr($f["CD_COR_GENERO"],5,2),16,10);
		$this->SetTextColor($colorR,$colorG,$colorB);

		$this->Cell(56, 5, utf8_encode($f["DS_CARGO"]), 0, false, 'L', true, false, 1);
		$this->setX(146);
		$this->Cell(5, 5, $f["IDADE_HOJE"], 0, false, 'L', true, false, 1);
		$this->SetTextColor(0,0,0);
		$this->setX(150);
		$this->Cell(20, 5, strftime("%d/%m/%Y",strtotime($f["DT_NASC"])), 0, false, 'L', true, false, 1);
		$this->setX(170);
		$this->Cell(35, 5, trim($f["FONE_RES"]."   ".$f["FONE_CEL"]), 0, false, 'L', true, false, 1);
		$this->posY+=5;
		$this->lineAlt = !$this->lineAlt;
	}
	
	public function addLineCount($result){
		$this->posY+=2;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(80,80,80);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(200, 6, "Total de Membros Ativos: ".$result->RecordCount(), 0, false, 'C', true);
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemAtivos_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

/*
$aM = explode(",",fRequest("m"));
if ( !isset($aM) || count($aM) == 0 ):
	exit("SELECIONE OS MESES QUE DESEJA IMPRIMIR AS FICHAS DE CHAMADA!");
endif;
$u = fRequest("u");
$aU = explode(",",$u);
if ( !isset($aU) || count($aU) == 0 ):
	exit("SELECIONE AS UNIDADES QUE DESEJA IMPRIMIR AS FICHAS DE CHAMADA!");
endif;
*/
$pdf = new LISTAATIVOSALFA();

fConnDB();
$pdf->newPage();
$result = $GLOBALS['conn']->Execute("
	SELECT ca.NM, ca.CD_CARGO, ca.DS_CARGO, ca.DT_NASC, ca.FONE_RES, ca.FONE_CEL, ca.IDADE_HOJE, ta.CD_COR_GENERO
	FROM CON_ATIVOS ca
 INNER JOIN TAB_UNIDADE ta ON (ta.ID = ca.ID_UNIDADE)
	ORDER BY ca.NM
");
foreach ( $result as $ra => $f ):
	$pdf->startTransaction();
	$start_page = $pdf->getPage();
	$pdf->addLine($f);
	if  ($pdf->getNumPages() != $start_page):
		$pdf->rollbackTransaction(true);
		$pdf->newPage();
		$pdf->addLine($f);
	else:
		$pdf->commitTransaction();     
	endif;
endforeach;

$pdf->startTransaction();
$start_page = $pdf->getPage();
$pdf->addLineCount($result);
if  ($pdf->getNumPages() != $start_page):
	$pdf->rollbackTransaction(true);
	$pdf->newPage();
	$pdf->addLineCount($result);
else:
	$pdf->commitTransaction();     
endif;

$pdf->download();
exit;
?>