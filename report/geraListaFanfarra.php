<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAFANFARRA extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $posY;
	private $lineAlt;
	public $filter;
	
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
		$this->SetTitle('Listagem de Membros da Fanfarra');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Fanfarra, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}
	
	public function fSetFilter($filter){
	    $this->filter = $filter;
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
		$this->Cell(185, 9, "Listagem de Membros da Fanfarra", 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->setXY(20,15);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->Cell(185, 5, $GLOBALS['pattern']->getCDS(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(255,102,51);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, 22);
		
		if ($this->filter == "A"):
    		$this->Cell(125, 7, "Nome Completo", 0, false, 'L', true);
    		$this->setXY(130, 22);
    		$this->Cell(75, 7, "Instrumento", 0, false, 'L', true);
		else:
		    $this->Cell(175, 7, "Nome Completo", 0, false, 'L', true);
    		$this->setXY(180, 22);
    		$this->Cell(25, 7, "Instrumento", 0, false, 'C', true);
    	endif;
		$this->posY = 29;
	}
	
	private function addGrupoInstrumentoTitle($af) {
		$this->setCellPaddings(2,1,1,1);
		$rsM = $GLOBALS['conn']->Execute("
          SELECT ca.NM, ca.CD_FANFARRA, tti.DS
        	FROM CON_ATIVOS ca
      INNER JOIN CAD_INSTRUMENTO ci ON (ci.CD = ca.CD_FANFARRA)
      INNER JOIN TAB_TP_INSTRUMENTO tti ON (tti.ID = ci.ID_TP)
           WHERE ci.ID_TP = ?
        ORDER BY ca.NM
		", array( $af["ID"] ) );
		if ( !$rsM->EOF ):
    		$this->SetFillColor(80, 80, 80);
    		$this->SetTextColor(255,255,255);
    		$this->setXY(5, $this->posY);
    		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 14);
    		$this->Cell(200, 12, ($af["DS"]) ." (".$rsM->RecordCount().")", 0, false, 'L', true);
    		$this->posY += 12;
    		$this->lineAlt = false;
    	endif;
		
		return $rsM;
	}

	private function addLine($f){
		$this->setCellPaddings(1,1,1,1);
		if ($this->lineAlt):
		    $this->SetFillColor(240,240,240);
		else:
		    $this->SetFillColor(255,255,255);
		endif;
		$this->setXY(5, $this->posY);
		if ($this->filter == "A"):
    		$this->Cell(125, 10, $f["NM"], 0, false, 'L', true, false, 1);
    		$this->setX(130);
    		$this->Cell(75, 10, $f["CD_FANFARRA"] ." - ". $f["DS"], 0, false, 'L', true, false, 1);
    	else:
    		$this->Cell(175, 10, $f["NM"], 0, false, 'L', true, false, 1);
    		$this->setX(180);
    		$this->Cell(25, 10, $f["CD_FANFARRA"], 0, false, 'C', true, false, 1);
    	endif;
        $this->posY += 10;

		$this->lineAlt = !$this->lineAlt;
	}
	
	public function addGroupDetail($rsM){
        $this->SetFont(PDF_FONT_NAME_MAIN, 'N', 12);
    	$this->SetTextColor(0,0,0);
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
	}
	
	public function addGrupoInstrumento($af) {
		$this->startTransaction();
		$start_page = $this->getPage();
		$rsM = $this->addGrupoInstrumentoTitle($af);
		if  ($this->getNumPages() != $start_page):
			$this->rollbackTransaction(true);
			$this->newPage();
			$rsM = $this->addGrupoInstrumentoTitle($af);
		else:
			$this->commitTransaction();
		endif;
		
        $this->addGroupDetail($rsM);
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemFanfarra_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$pdf = new LISTAFANFARRA();
$pdf->filter = fRequest("filter");

fConnDB();
$pdf->newPage();

//AGRUPAMENTO POR INSTRUMENTO
if ($pdf->filter == "I"):
    $result = $GLOBALS['conn']->Execute("
    	SELECT *
    	FROM TAB_TP_INSTRUMENTO
    	ORDER BY NR_SEQ
    ");
    foreach ( $result as $ra => $f ):
    	$pdf->addGrupoInstrumento($f);
    endforeach;
    
//AGRUPAMENTO ALFABETICO
else:
    $result = $GLOBALS['conn']->Execute("
      SELECT ca.NM, ca.CD_FANFARRA, tti.DS
    	FROM CON_ATIVOS ca
  INNER JOIN CAD_INSTRUMENTO ci ON (ci.CD = ca.CD_FANFARRA)
  INNER JOIN TAB_TP_INSTRUMENTO tti ON (tti.ID = ci.ID_TP)
       WHERE ca.CD_FANFARRA IS NOT NULL
    ORDER BY ca.NM
    ");
    $pdf->addGroupDetail($result);
endif;

$pdf->download();
exit;
?>