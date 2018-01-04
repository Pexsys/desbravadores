<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTACLASSE extends TCPDF {
	
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
		$this->SetTitle('Listagem de Membros por Classe');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Classes, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ig") ) ));
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
		$this->Cell(185, 9, "Listagem de Membros por Classe", 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->setXY(20,15);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->Cell(185, 5, $GLOBALS['pattern']->getCDS(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(0,0,0);
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
	
	private function addGrupoAprendTitle($af) {
		$rsM = $GLOBALS['conn']->Execute("
			SELECT ca.ID_CAD_PESSOA, ca.NM, ca.CD_CARGO, ca.DS_CARGO, ca.DT_NASC, ca.FONE_RES, ca.FONE_CEL
			FROM CON_ATIVOS ca
			INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
			WHERE ah.DT_CONCLUSAO IS NULL
			  AND ah.ID_TAB_APREND = ?
			ORDER BY SUBSTR(ca.CD_CARGO,1,2), ca.IDADE_HOJE, ca.NM
		", array( $af["ID"] ) );
		if (!$rsM->EOF):
			$this->setCellPaddings(2,1,1,1);
			$colorR = base_convert(substr($af["CD_COR"],1,2),16,10);
			$colorG = base_convert(substr($af["CD_COR"],3,2),16,10);
			$colorB = base_convert(substr($af["CD_COR"],5,2),16,10);
			
			$this->SetFillColor($colorR, $colorG, $colorB);
			if ($af["ID"] > 10):
				$this->SetTextColor(0,0,0);
			else:
				$this->SetTextColor(255,255,255);
			endif;
			$this->setXY(5, $this->posY);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
			$this->Cell(200, 7, $af["DS_ITEM"]."-".$af["NR_IDADE_MINIMA"]." anos (".$rsM->RecordCount().")", 0, false, 'L', true);
			$this->posY += 7;
			$this->lineAlt = false;
			$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
			$this->SetTextColor(0,0,0);
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
		$this->Cell(85, 5, $f["NM"], 0, false, 'L', true, false, 1);
		$this->setX(90);
		if (fStrStartWith($f["CD_CARGO"], "1-")):
			$this->SetTextColor(255,0,0);
		endif;
		$this->Cell(50, 5, $f["DS_CARGO"], 0, false, 'L', true, false, 1);
		$this->SetTextColor(0,0,0);
		$this->setX(140);
		$this->Cell(30, 5, $f["IDADE_HOJE"]." ".strftime("%d/%m/%Y",strtotime($f["DT_NASC"])), 0, false, 'C', true, false, 1);
		$this->setX(170);
		$this->Cell(35, 5,  trim($f["FONE_RES"]."   ".$f["FONE_CEL"]), 0, false, 'L', true, false, 1);
		$this->posY+=5;
		$this->lineAlt = !$this->lineAlt;
	}
	
	public function addGrupoAprendizado($af) {
		$this->startTransaction();
		$start_page = $this->getPage();
		$rsM = $this->addGrupoAprendTitle($af);
		if (!$rsM->EOF):
			if  ($this->getNumPages() != $start_page):
				$this->rollbackTransaction(true);
				$this->newPage();
				$rsM = $this->addGrupoAprendTitle($af);
			else:
				$this->commitTransaction();
			endif;

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
		endif;
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemClasse_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$filter = fRequest("filter");
$pdf = new LISTACLASSE();

fConnDB();
$pdf->newPage();
$result = $GLOBALS['conn']->Execute("
	SELECT DISTINCT ta.CD_ITEM_INTERNO, ta.NR_IDADE_MINIMA, ta.DS_ITEM, ta.CD_COR, ta.ID
	FROM APR_HISTORICO ah
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	WHERE ah.DT_CONCLUSAO IS NULL
	  AND ta.TP_ITEM = ?
	  ". (isset($filter) && !is_null($filter) && $filter !== "null" ? " AND ta.ID IN ($filter)" : "" ) ."
	ORDER BY ta.CD_ITEM_INTERNO
", array("CL") );
foreach ( $result as $ra => $f ):
	$pdf->addGrupoAprendizado($f);
endforeach;
$pdf->download();
exit;
?>