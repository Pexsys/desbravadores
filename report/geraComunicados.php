<?php
@require_once('../include/functions.php');

class COMUNICADO extends TCPDF {

	//lines styles
	private $stLine;
	private $hPoint;

	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$this->stLine = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => '2,4', 'color' => array(110, 110, 110));
		$this->hPoint = 0;

		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetTitle('Geração automática de Comunicados');
		$this->SetSubject(PATTERNS::getClubeDS(array("cl","nm")));
		$this->SetKeywords('Comunicados, ' . str_replace(" ", ", ", PATTERNS::getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->SetTopMargin(4);
		$this->SetFooterMargin(0);
		$this->SetLeftMargin(4);
		$this->SetRightMargin(4);
		$this->SetHeaderMargin(0);
		$this->SetFooterMargin(0);
		$this->setHtmlVSpace(
			array('p' => array(0 => array('h' => 0, 'n' => 0),
							   1 => array('h' => 0, 'n' => 0)
						),
				 'br' => array(0 => array('h' => 0, 'n' => 0),
							   1 => array('h' => 0, 'n' => 0)
				 		)
			)
		);
	}

 	public function Header() {
	}

	public function Footer() {
	}

	public function newPage() {
		$this->AddPage();
		$this->setXY(0,0);
		$this->hPoint = 0;
	}

	public function addComunicado( $line ) {
		$this->startTransaction();
		$start_page = $this->getPage();
		$this->modelComunicado( $line );
		if  ($this->getNumPages() != $start_page) {
			$this->rollbackTransaction(true);
			$this->newPage();
			$this->modelComunicado( $line );
		}else{
			$this->commitTransaction();
		}
		$this->aP++;
	}

	public function modelComunicado( $line ){
		$this->hPoint += 6;

		$this->SetY($this->hPoint);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 14);
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
		$this->SetTextColor(255,0,0);
		$this->Cell(20, 0, "#".fStrZero($line["ID"],3), 0, false, 'L', false, false, 1, false, 'C', 'C');
		$this->SetX(60);
		$this->SetTextColor(0,0,0);
		$this->Cell(0, 0, "Comunicado Anual #".$line["CD"]." [".strftime("%d/%m/%Y",strtotime($line["DH"]))."]", 0, 0, 'L', false, false, 0, false, 'C', 'C');
		$this->Image("img/logo.jpg", 197, $this->hPoint-4, 7, 8, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		//writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		$this->SetFont(PDF_FONT_NAME_MAIN);
		$this->hPoint += 5;
		$this->writeHTMLCell(200, 0, 5, $this->hPoint, $line["TXT"],
			array('LTRB' => array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0))),
			0, 0, true, 'J', true);

		$this->hPoint += $this->getLastH()+4;
		$this->Line(0, $this->hPoint, 220, $this->hPoint, $this->stLine);
	}

	public function download() {
		$this->lastPage();
		$this->Output("Comunicados_".date('Y-m-d_H:i:s').".pdf", 'I');
	}
}

$comunicadoID = fRequest("id");
if ( !isset($comunicadoID) || empty($comunicadoID) || stristr($comunicadoID, "indispon") ):
	echo "COMUNICADO N&Atilde;O ENCONTRADO!";
	exit;
endif;
$qtd = fRequest("q");

$pdf = new COMUNICADO();
$result = CONN::get()->Execute("
	SELECT *
	  FROM CAD_COMUNICADO
	 WHERE ID = ?
", array($comunicadoID) );
if (!$result->EOF):
	$pdf->newPage();
	for ($i=0;$i<$qtd;$i++):
		$pdf->addComunicado( $result->fields );
		//echo $result->fields['TXT'];
	endfor;
	$pdf->download();
endif;
exit;
?>
