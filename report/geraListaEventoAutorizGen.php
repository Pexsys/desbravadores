<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAEVENTOAUTORIZGENERO extends TCPDF {
	
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
		
		$this->SetTitle('Listagem de Participantes do Evento');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Evento, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ibd") ) ));
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
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 16);
		$this->Cell(185, 6, "LISTAGEM PARA CONTROLE DE AUTORIZAÇÕES - ".($this->header["TP_SEXO"] == "F" ? "MENINAS" : "MENINOS"), 0, false, 'C', false, false, false, false, 'T', 'M');
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
		$this->SetFillColor(6,156,16);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(35, 6, "Controle", 0, false, 'C', true);
		$this->setXY(40, $this->posY);
		$this->Cell(85, 6, "Nome Completo", 0, false, 'L', true);
		$this->setXY(125, $this->posY);
		$this->Cell(30, 6, "Idade/Nasc.", 0, false, 'L', true);
		$this->setXY(155, $this->posY);
		$this->Cell(50, 6, "Telefones", 0, false, 'L', true);
		$this->posY += 7;
	}

	public function setHeaderFields($header){
		$this->header = $header;
	}

	public function addGrupoGenero($g) {
		$this->setHeaderFields($g);
		$this->newPage();
		
		$rsM = CONN::get()->Execute("
			SELECT ca.NM, at.CD_CARGO, IF(ca.TP_SEXO='F',cg.DSF,cg.DSM) AS DS_CARGO, ca.DT_NASC, ca.FONE_RES, ca.FONE_CEL, ca.IDADE_HOJE
			FROM EVE_SAIDA es
			INNER JOIN EVE_SAIDA_MEMBRO esp on (esp.ID_EVE_SAIDA = es.ID AND esp.FG_AUTORIZ = 'S')
			INNER JOIN CAD_MEMBRO cm on (cm.ID = esp.ID_CAD_MEMBRO)
			INNER JOIN CAD_ATIVOS at on (at.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO AND at.NR_ANO = YEAR(es.DH_R))
			INNER JOIN TAB_CARGO cg ON (cg.CD = at.CD_CARGO)
			INNER JOIN CON_PESSOA ca on (ca.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
		    WHERE es.ID = ?
			  AND ca.TP_SEXO = ?
			ORDER BY ca.NM
		", array( $g["ID_EVE_SAIDA"], $g["TP_SEXO"] ) );
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
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
		$this->setCellPaddings(1,1,1,1);
		if ($this->lineAlt):
			$this->SetFillColor(240,240,240);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->setXY(5, $this->posY);
		$this->Cell(34, 7, "", 1, false, 'C', true);
		$this->setX(40);
		$this->Cell(85, 7, $f["NM"], 0, false, 'L', true, false, 1);
		$this->setX(125);
		$this->Cell(5, 7, $f["IDADE_HOJE"], 0, false, 'L', true, false, 1);
		$this->setX(130);
		$this->Cell(25, 7, strftime("%d/%m/%Y",strtotime($f["DT_NASC"])), 0, false, 'L', true, false, 1);
		$this->setX(155);
		$this->Cell(50, 7, trim($f["FONE_RES"]."   ".$f["FONE_CEL"]), 0, false, 'L', true, false, 1);
		$this->posY+=7;
		$this->lineAlt = !$this->lineAlt;
	}
	
	public function addLineCount($result){
		$this->posY+=2;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(6,156,16);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(200, 6, "Total de Autorizações: ".$result->RecordCount(), 0, false, 'C', true);
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemParticipantesAutorizGen_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$eveID = fRequest("eve");
$pdf = new LISTAEVENTOAUTORIZGENERO();


$result = CONN::get()->Execute("
	SELECT DISTINCT es.DS, es.DS_TEMA, es.DS_ORG, es.DS_DEST, 
					esp.ID_EVE_SAIDA, ca.TP_SEXO 
   	FROM EVE_SAIDA es
	INNER JOIN EVE_SAIDA_MEMBRO esp on (esp.ID_EVE_SAIDA = es.ID)
	INNER JOIN CAD_MEMBRO cm on (cm.ID = esp.ID_CAD_MEMBRO)
	INNER JOIN CAD_ATIVOS at on (at.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO AND at.NR_ANO = YEAR(es.DH_R))
	INNER JOIN TAB_CARGO cg ON (cg.CD = at.CD_CARGO)
	INNER JOIN CON_PESSOA ca on (ca.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
    WHERE es.ID = ?
	ORDER BY ca.TP_SEXO
", array($eveID) );
if (!$result->EOF):
	foreach ( $result as $o => $g ):
		$pdf->addGrupoGenero($g);
	endforeach;
	$pdf->download();
endif;
exit;
?>