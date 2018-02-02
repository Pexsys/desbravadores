<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAMATERIAIS extends TCPDF {

	//lines styles
	private $stLine;
	private $posY;
	private $lineAlt;
	private $eventoID;
	private $header;
	public $tipoUniforme;

	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$this->stLine = array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
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
		$this->SetTitle('Listagem Alfabética de Materiais');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Uniformes, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}

	public function setTipoUniforme($tipoUniforme){
	    $this->tipoUniforme = $tipoUniforme;
	}

	public function setEventoID($eventoID){
		$this->eventoID = $eventoID;
	}

	public function setHeaderFields($header){
		$this->header = $header;
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
		$this->Cell(185, 6, "LISTAGEM PARA CONTROLE DE ". ($this->tipoUniforme == "A" ? "AGASALHOS" : "CAMISETAS: ______________" ), 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->posY += 7;

		$this->setXY(20,$this->posY);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
		$this->Cell(185, 5, $GLOBALS['pattern']->getCDS(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
		$this->posY += 6;

		if (!empty($this->eventoID)):
			$this->setXY(20,$this->posY);
			$this->SetTextColor(0,0,0);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
			$this->Cell(185, 5, ($this->header["DS"] . (!is_null($this->header["DS_TEMA"]) ? " - ".$this->header["DS_TEMA"] : "")  ." - ". $this->header["DS_DEST"]), 0, false, 'C', false, false, false, false, 'T', 'M');
		endif;
		$this->posY += 5;

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(149,107,164);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(135, 6, "Nome Completo", 0, false, 'L', true);
		$this->setX(140);
		$this->Cell(25, 6, "Utiliza Nº", 0, false, 'C', true);
		$this->setX(165);
		$this->Cell(20, 6, "Nº Retirada", 0, false, 'L', true);
		$this->setX(185);
		$this->Cell(20, 6, "Nº Devolução", 0, false, 'L', true);
		$this->posY += 7;
	}

	public function addLineUniforme($f){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 10);
		$this->setCellPaddings(1,0,1,0);
		if ($this->lineAlt):
			$this->SetFillColor(240,240,240);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->SetTextColor(0,0,0);
		$this->setXY(80, $this->posY);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 12);
		$this->Cell(30, 8, is_null($f["TP"]) ? "-" : $f["TP"], 0, false, 'L', true, false, 1);
		$this->setX(110);
		$this->Cell(25, 8, $f["QTD"], 0, false, 'C', true, false, 1);
		$this->posY+=9;
		$this->lineAlt = !$this->lineAlt;
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
		$this->Cell(135, 8, $f["NM"], 0, false, 'L', true, false, 1);
		$this->setX(140);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 11);
		$this->Cell(25, 8, $f["TP"], 0, false, 'C', true, false, 1);
		$this->setX(165);
		$this->Cell(19, 8, "", 1, false, 'L', true, false, 1);
		$this->setX(186);
		$this->Cell(19, 8, "", 1, false, 'L', true, false, 1);
		$this->posY+=9;
		$this->lineAlt = !$this->lineAlt;
	}

	public function addLineCount($result){
		$this->posY+=2;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(149,107,164);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(200, 6, "Total de Membros Ativos: ".$result->RecordCount(), 0, false, 'C', true);
		$this->posY+=9;
	}

	public function geraResumo($eventoID = null){
		$FIELD = ( $this->tipoUniforme == "C" ? "TP_CAMISETA" : "TP_AGASALHO" );
		$bind = array();

		if (!empty($eventoID)):
			$str = "
				SELECT at.$FIELD AS TP, COUNT(*) AS QTD
				FROM CAD_MEMBRO cm
				INNER JOIN EVE_SAIDA_MEMBRO esp ON (esp.ID_CAD_MEMBRO = cm.ID AND esp.ID_EVE_SAIDA = ?)
				INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA)
				INNER JOIN CAD_ATIVOS at on (at.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO AND at.NR_ANO = YEAR(es.DH_R))
				INNER JOIN CON_PESSOA ca on (ca.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
				GROUP BY at.$FIELD
				ORDER BY ca.NM
			";
			$bind[] = $eventoID;
		else:
			$str = "
					SELECT ca.$FIELD AS TP, COUNT(*) AS QTD
					FROM CON_ATIVOS ca
					LEFT JOIN TAB_TAMANHOS tt ON (tt.TP = ? AND tt.CD = ca.$FIELD)
					GROUP BY ca.$FIELD
					ORDER BY tt.ORD
			";
			$bind[] = $this->tipoUniforme;
		endif;
		$result = CONN::get()->Execute($str,$bind);
		foreach ( $result as $ra => $f ):
			$this->addLineUniforme($f);
		endforeach;
	}

	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemMateriais_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$pdf = new LISTAMATERIAIS();
$pdf->setTipoUniforme(fRequest("filter"));
$eventoID = fRequest("eve");
$pdf->setEventoID($eventoID);



if (!empty($eventoID)):
	$str = "
		SELECT ca.NM, ". ($pdf->tipoUniforme == "C" ? " at.TP_CAMISETA" : "at.TP_AGASALHO") ." AS TP,
		es.DS, es.DS_TEMA, es.DS_ORG, es.DS_DEST
		FROM CAD_MEMBRO cm
		INNER JOIN EVE_SAIDA_MEMBRO esp ON (esp.ID_CAD_MEMBRO = cm.ID AND esp.ID_EVE_SAIDA = $eventoID)
		INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA)
		INNER JOIN CAD_ATIVOS at on (at.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO AND at.NR_ANO = YEAR(es.DH_R))
		INNER JOIN CON_PESSOA ca on (ca.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
		ORDER BY ca.NM
	";
else:
	$str = "
		SELECT ca.NM, ". ($pdf->tipoUniforme == "C" ? " ca.TP_CAMISETA" : "ca.TP_AGASALHO") ." AS TP
		FROM CON_ATIVOS ca
		ORDER BY ca.NM
	";
endif;

$result = CONN::get()->Execute($str);
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

	//RESUMO GERAL
	$pdf->startTransaction();
	$start_page = $pdf->getPage();
	$pdf->geraResumo($eventoID);
	if  ($pdf->getNumPages() != $start_page):
		$pdf->rollbackTransaction(true);
		$pdf->newPage();
		$pdf->geraResumo($eventoID);
	else:
		$pdf->commitTransaction();
	endif;

	$pdf->download();
endif;
exit;
?>
