<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAMATERIAIS extends TCPDF {
	
	//lines styles
	private $stLine;
	private $posY;
	private $lineAlt;
	public $tipoUniforme;
	
	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$this->stLine = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
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
		$this->SetTitle('Listagem Alfabética de Presença na Reunião de Pais');
		$this->SetSubject('Clube Pioneiros');
		$this->SetKeywords('Desbravadores, Especialidades, Pioneiros, Capão Redondo');
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}
	
	public function setTipoUniforme($tipoUniforme){
	    $this->tipoUniforme = $tipoUniforme;
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
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 15.5);
		$this->Cell(185, 9, "Listagem de Presença - Reunião de Pais: _____/_____/________", 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->setXY(20,15);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->Cell(185, 5, fClubeID(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(69,98,135);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, 22);
		$this->Cell(135, 6, "Nome Completo", 0, false, 'L', true);
		$this->setXY(140, 22);
		$this->Cell(65, 6, "Assinatura", 0, false, 'C', true);
		$this->posY = 29;
	}

	public function addLine($f){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
		$this->setCellPaddings(1,0,1,0);
		if ($this->lineAlt):
			$this->SetFillColor(240,240,240);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->setXY(5, $this->posY);
		$this->Cell(135, 10, utf8_encode($f["NM"]), 0, false, 'L', true, false, 1);
		
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 5);
		$this->Line(140, $this->posY+8, 205, $this->posY+8, $this->stLine3);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 5);
		$this->setXY(140, $this->posY+10);
		$this->Cell(0, 0, utf8_encode($f["NM_RESP"] ." - ". $f["DOC_RESP"] ." - ". $f["TEL_RESP"]), 0, false, 'L', false, false, 1, false, 'L', 'C');

		$this->posY+=12;
		$this->lineAlt = !$this->lineAlt;
	}
	
	public function addLineCount($result){
		$this->posY+=2;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(69,98,135);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(200, 6, "Total de Menores Ativos: ".$result->RecordCount(), 0, false, 'C', true);
		$this->posY+=9;
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemPresencaPais_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$pdf = new LISTAMATERIAIS();
$pdf->setTipoUniforme(fRequest("filter"));

fConnDB();
$pdf->newPage();
$result = $GLOBALS['conn']->Execute("
	SELECT *
	FROM CON_ATIVOS
	WHERE IDADE_HOJE < 18
	ORDER BY NM
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