<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class DIARIO extends TCPDF {
	
	//lines styles
	private $stLine;
	private $hPoint;
	
	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$this->stLine = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => '2,4', 'color' => array(110, 110, 110));
		$this->hPoint = 0;
		
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetTitle('Geração automática de Diário de Classe');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Diário, Classe, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ig") ) ));
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
	
	public function addRegistro( $line ) {
		$this->startTransaction();
		$start_page = $this->getPage();
		$this->modelRegistro( $line );
		if  ($this->getNumPages() != $start_page) {
			$this->rollbackTransaction(true);
			$this->newPage();
			$this->modelRegistro( $line );
		}else{
			$this->commitTransaction();     
		}	
		$this->aP++;
	}
	
	public function modelRegistro( $line ){
		$this->hPoint += 6;

		$this->SetY($this->hPoint);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 14);
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
		$this->SetTextColor(255,0,0);
		$this->Cell(20, 0, "#".fStrZero($line["ID"],3), 0, false, 'L', false, false, 1, false, 'C', 'C');
		$this->SetX(60);
		$this->SetTextColor(0,0,0);
		$this->Cell(0, 0, "Registro ".($line["TP"] == "P"?"PLANEJADO":"CONCLUÍDO")." #".$line["CD"]." [".strftime("%d/%m/%Y",strtotime($line["DH"]))."]", 0, 0, 'L', false, false, 0, false, 'C', 'C');
		$this->Image("img/logo.jpg", 197, $this->hPoint-4, 7, 8, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		//writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		$this->hPoint += 10;
		$this->SetXY(5,$this->hPoint);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 14);
		$this->Cell(0, 5, "Para: ", 0, 0, 'L', false, false, 0, false, 'C', 'C');
		$this->SetX(18);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 14);
		$this->Cell(0, 5, $line["NM"], 0, 0, 'L', false, false, 0, false, 'C', 'C');
		$this->hPoint += 5;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 12);
		$this->writeHTMLCell(200, 0, 5, $this->hPoint, ($line["TXT"]."<p style=\"font-size:12px\"><i>Registro inserido por ".$line["DS_USUARIO"]."</i></p>"), 
			array('LTRB' => array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0))), 
			0, 0, true, 'J', true);
		
		$this->hPoint += $this->getLastH()+4;
		$this->Line(0, $this->hPoint, 220, $this->hPoint, $this->stLine);	
	}

	public function download() {
		$this->lastPage();
		$this->Output("DiarioClasse_".date('Y-m-d_H:i:s').".pdf", 'I');
	}
}
fConnDB();

$where = "";
$id = fRequest("id");
if (isset($id) && !empty($id)):
	$where .= " AND cd.ID IN ($id)";
endif;

$ic = fRequest("ic");
if (isset($ic) && !empty($ic)):
	$where .= " AND cd.ID_TAB_APREND IN ($ic)";
endif;

$it = fRequest("it");
if (isset($it) && !empty($it)):
	$where .= " AND cd.ID_TAB_APR_ITEM IN ($it)";
endif;

$pdf = new DIARIO();
$result = $GLOBALS['conn']->Execute("
		SELECT cd.ID, cd.SQ, ta.DS_ITEM, taa.CD AS CD_AREA, taa.DS AS DS_AREA, tap.CD_REQ_INTERNO, tap.DS, cd.DH, cd.FG_PEND, cd.TXT
		FROM CAD_DIARIO cd
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = cd.ID_TAB_APREND)
	LEFT JOIN TAB_APR_ITEM tap ON (tap.ID = cd.ID_TAB_APR_ITEM)
	LEFT JOIN TAB_APR_AREA taa ON (taa.ID = tap.ID_TAB_APR_AREA)
	".(empty($where) ? "" : "WHERE ".substr($where,5) )." 
	ORDER BY cd.SQ
");
if (!$result->EOF):
	$pdf->newPage();
	foreach ($result as $k => $fields):
		$pdf->addRegistro( $fields );
	endforeach;
	$pdf->download();
endif;
exit;
?>