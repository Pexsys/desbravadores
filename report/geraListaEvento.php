<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAEVENTOALFA extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $posY;
	private $lineAlt;
	private $header;
	
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
		$this->SetTitle('Listagem de Participantes do Evento');
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
		$this->posY = 5;
		
		$this->setXY(20,$this->posY);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 15);
		$this->Cell(185, 7, utf8_encode( $this->header["DS"] . (!is_null($this->header["DS_TEMA"]) ? " - ".$this->header["DS_TEMA"] : "") ), 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->posY += 7;
		
		$this->setXY(20,$this->posY);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
		$this->Cell(185, 5, utf8_encode($this->header["DS_ORG"] . " - " .$this->header["DS_DEST"]), 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->posY += 5;
		
		$this->setXY(20,$this->posY);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->Cell(185, 5, fClubeID(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
		$this->posY += 5;
		
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(80,80,80);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(85, 6, "Nome Completo", 0, false, 'L', true);
		$this->setXY(90, $this->posY);
		$this->Cell(50, 6, "Cargo", 0, false, 'L', true);
		$this->setXY(140, $this->posY);
		$this->Cell(30, 6, "Idade/Nasc.", 0, false, 'C', true);
		$this->setXY(170, $this->posY);
		$this->Cell(35, 6, "Telefones", 0, false, 'L', true);
		$this->posY += 6;
	}

	public function setHeaderFields($header){
		$this->header = $header;
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
		$this->Cell(200, 6, "Total de Membros no Evento: ".$result->RecordCount(), 0, false, 'C', true);
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemParticipantesEvento_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$eveID = fRequest("id");
$pdf = new LISTAEVENTOALFA();

fConnDB();
$result = $GLOBALS['conn']->Execute("
	SELECT 
			es.DS, es.DS_TEMA, es.DS_ORG, es.DS_DEST,
			esp.ID AS ID_EVE_PESSOA, 
			ca.ID, ca.NM, ca.CD_CARGO, ca.DS_CARGO, ca.DT_NASC, ca.FONE_RES, ca.FONE_CEL, ca.IDADE_HOJE, 
			YEAR(es.DH_R)-YEAR(ca.DT_NASC) - IF(DATE_FORMAT(ca.DT_NASC,'%m%d')>DATE_FORMAT(es.DH_R,'%m%d'),1,0) AS IDADE_EVENTO_FIM,
			esp.FG_AUTORIZ 
	FROM EVE_SAIDA es
    INNER JOIN EVE_SAIDA_PESSOA esp on (esp.ID_EVE_SAIDA = es.ID)
    INNER JOIN CON_ATIVOS ca on (ca.ID = esp.ID_CAD_PESSOA)
    WHERE es.ID = ?
	ORDER BY ca.NM
", array($eveID) );
if (!$result->EOF):
	$pdf->setHeaderFields($result->fields);

	$pdf->newPage();
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
endif;
exit;
?>