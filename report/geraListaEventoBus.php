<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAEVENTOBUS extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $posY;
	private $lineAlt;
	private $header;
	public $seq;
	
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
		$this->SetTitle('Listagem de Passageiros do Evento');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Onibus, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ibd") ) ));
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
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 20);
		$this->Cell(185, 6, "LISTAGEM DE PASSAGEIROS - ÔNIBUS ". $this->header["BUS"], 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->posY += 8;

		$this->setXY(20,$this->posY);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
		$this->Cell(185, 5, $GLOBALS['pattern']->getCDS(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
		$this->posY += 5;
		
		$this->setXY(20,$this->posY);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->Cell(185, 5, ($this->header["DS"] . (!is_null($this->header["DS_TEMA"]) ? " - ".$this->header["DS_TEMA"] : "")  ." - ". $this->header["DS_DEST"]), 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->posY += 5;

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(218,165,32);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(8, 6, "Seq.", 0, false, 'C', true);
		$this->setX(13);
		$this->Cell(87, 6, "Nome Completo", 0, false, 'L', true);
		$this->setX(100);
		$this->Cell(18, 6, "Nascimento", 0, false, 'C', true);
		$this->setX(118);
		$this->Cell(24, 6, "Identificação", 0, false, 'L', true);
		$this->setX(142);
		$this->Cell(21, 6, "CPF", 0, false, 'C', true);
		$this->setX(163);
		$this->Cell(42, 6, "Telefones", 0, false, 'L', true);
		$this->posY += 6;
	}

	public function setHeaderFields($header){
		$this->header = $header;
	}
	
	public function addLineCount($result){
		$this->posY+=2;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(218,165,32);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(200, 6, "Total de Passageiros: ".$result->RecordCount(), 0, false, 'C', true);
	}
	
	public function addGrupoBus($g) {
	    $this->setHeaderFields($g);
	    $this->newPage();
	    
	    $rsM = $GLOBALS['conn']->Execute("
		  SELECT ca.NM, ca.DT_NASC, ca.FONE_RES, ca.FONE_CEL, ca.NR_DOC, ca.NR_CPF, ca.NR_CPF_RESP
		  FROM EVE_SAIDA_MEMBRO esp
		  INNER JOIN CAD_MEMBRO cm on (cm.ID = esp.ID_CAD_MEMBRO)
		  INNER JOIN CON_PESSOA ca on (ca.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
		    WHERE esp.ID_EVE_SAIDA = ?
			  AND esp.BUS = ?
			ORDER BY ca.NM
		", array( $g["ID"], $g["BUS"]) );
		foreach ($rsM as $k => $f):
			$this->startTransaction();
			$start_page = $this->getPage();
			$this->addLine($f);
			if  ($this->getNumPages() != $start_page):
				$this->rollbackTransaction(true);
				$this->newPage();
				$this->addLine($f);
			else:
				$this->commitTransaction();     
			endif;
		endforeach;
		
		$this->startTransaction();
		$start_page = $this->getPage();
		$this->addLineCount($rsM);
		if  ($this->getNumPages() != $start_page):
			$this->rollbackTransaction(true);
			$this->newPage();
			$this->addLineCount($rsM);
		else:
			$this->commitTransaction();
		endif;
	}
	
	public function addLine($f){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->setCellPaddings(1,1,1,1);
		$this->SetTextColor(0,0,0);
		if ($this->lineAlt):
			$this->SetFillColor(240,240,240);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->setXY(5, $this->posY);
		$this->Cell(8, 5, fStrZero( ++$this->seq, 2), 0, false, 'C', true);
		$this->setX(13);
		$this->Cell(87, 5, $f["NM"], 0, false, 'L', true, false, 1);
		$this->setX(100);
		$this->Cell(18, 5, strftime("%d/%m/%Y",strtotime($f["DT_NASC"])), 0, false, 'C', true, false, 1);
		$this->setX(118);
		$this->Cell(24, 5, $f["NR_DOC"], 0, false, 'L', true, false, 1);
		$this->setX(142);
		$this->Cell(21, 5, ( is_null($f["NR_CPF"]) ? fCPF($f["NR_CPF_RESP"]): fCPF($f["NR_CPF"]) ), 0, false, 'L', true, false, 1);
		$this->setX(163);
		$this->Cell(42, 5, trim($f["FONE_RES"]."   ".$f["FONE_CEL"]), 0, false, 'L', true, false, 1);
		$this->posY+=5;
		$this->lineAlt = !$this->lineAlt;
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemPassageirosEvento_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$eveID = fRequest("eve");
$pdf = new LISTAEVENTOBUS();

fConnDB();
$result = $GLOBALS['conn']->Execute("
	SELECT DISTINCT es.ID, es.DS, es.DS_TEMA, es.DS_ORG, es.DS_DEST, esp.BUS
      FROM EVE_SAIDA es
	  INNER JOIN EVE_SAIDA_MEMBRO esp on (esp.ID_EVE_SAIDA = es.ID AND esp.BUS IS NOT NULL)
	  INNER JOIN CAD_MEMBRO cm on (cm.ID = esp.ID_CAD_MEMBRO)
     WHERE es.ID = ?
    ORDER BY esp.BUS
", array($eveID) );

if (!$result->EOF):
    foreach ( $result as $o => $g ):
        $pdf->seq = 0;
    	$pdf->addGrupoBus($g);
    endforeach;
    $pdf->download();
endif;
exit;
?>