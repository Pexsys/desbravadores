<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class CHAMADA extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $unidade;
	private $mesAno;
	private $mesAtu;
	private $posY;
	private $xq;
	private $cdates;
	private $leftMin;
	
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

		$this->xq = 9;
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetTitle('Geração das fichas de chamada');
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
		$colorR = base_convert(substr($this->unidade["CD_COR_GENERO"],1,2),16,10);
		$colorG = base_convert(substr($this->unidade["CD_COR_GENERO"],3,2),16,10);
		$colorB = base_convert(substr($this->unidade["CD_COR_GENERO"],5,2),16,10);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor($colorR,$colorG,$colorB);
		
		/*WATERMARK
		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = K_PATH_IMAGES.'image_demo.jpg';
		$this->Image("img/unidade/".$this->unidade["ID"].".jpg", 10, 50, 190, 190, '', '', '', true, 5, '', false, true, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
		*/

		$this->setXY(0,0);
		$this->Image("img/logo.jpg", 5, 5, 14, 16, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->Image("img/unidade/".$this->unidade["ID"]."_E.jpg", 189, 5, 16, 16, 'JPG', '', 'T', false, 90, '', false, false, 0, false, false, false);
		//$this->ImageSVG("img/unidade/".$this->unidade["ID"]."_E.svg", 184, 1, 27, 27, $link='', $align='', $palign='', $border=0, $fitonpage=false);
		
		$this->setXY(20,5);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 25);
		$this->Cell(170, 9, "Controle de Presença da Unidade", 0, false, 'C', false, false, false, false, 'T', 'M');
		
		$this->setXY(20,15);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		$this->Cell(185, 5, fClubeID(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
		
		$this->cdates = $this->mesAno->RecordCount();
		$this->leftMin = 205-max($this->cdates*$this->xq, 40);
		$x = $this->leftMin;
		
		$this->setXY(5, 22);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 13);
		
		$colorR = base_convert(substr($this->unidade["CD_COR"],1,2),16,10);
		$colorG = base_convert(substr($this->unidade["CD_COR"],3,2),16,10);
		$colorB = base_convert(substr($this->unidade["CD_COR"],5,2),16,10);
		$this->SetFillColor($colorR,$colorG,$colorB);
		$this->SetTextColor(255,255,255);
		$this->setCellPaddings(3,0,0,0);
		$this->Cell($x-5, 8, utf8_encode($this->unidade["DS"]), 0, false, 'L', true);
		$this->setXY($x, 22);
		$this->SetFillColor(235,235,235);
		$this->SetTextColor($colorR,$colorG,$colorB);
		$this->setCellPaddings(0,0,0,0);
		$this->mesAtu = strftime("%m",strtotime($this->mesAno->fields["DTHORA_EVENTO_INI"])); 
		$this->Cell(205-$this->leftMin, 8, titleCase(strftime("%B/%Y",strtotime($this->mesAno->fields["DTHORA_EVENTO_INI"]))), 0, false, 'C', true);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 5);
		foreach ($this->mesAno as $k => $fm):
			$hr = fDescHora($fm["DTHORA_EVENTO_INI"]);
			$this->setXY($x,31);
			$this->MultiCell($this->xq, 5, utf8_encode(strftime("%d (%a)",strtotime($fm["DTHORA_EVENTO_INI"])))."\n$hr", 1, 'C', true, false, '', '', true, false, false, false, false);
			$x+=$this->xq;
		endforeach;
		$this->posY = 36;
	}
	
	public function addChamada($rm,$uf) {
		$this->mesAno = $rm;
		$this->unidade = $uf;

		//achar o nome e a area com select
		$this->newPage();

		$rsM = $GLOBALS['conn']->Execute("
			SELECT * 
			  FROM CON_ATIVOS 
			 WHERE ID_UNIDADE = ? 
			   AND FG_REU_SEM = ?
			ORDER BY CD_CARGO, NM
		", array( $uf["ID"], "S" ) );
		
		foreach ($rsM as $k => $f):
			$this->startTransaction();
			$start_page = $this->getPage();
			$this->addLine($f);
			if  ($this->getNumPages() != $start_page) {
				$this->rollbackTransaction(true);
				$this->newPage();
				$this->addLine($f);
			}else{
				$this->commitTransaction();     
			}
		endforeach;
	}
	
	private function addLine($f){
		$x = $this->leftMin;
		$h = 5;
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5,$this->posY);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);

		if ( strftime("%m",strtotime($f["DT_NASC"])) == $this->mesAtu ):
			$this->SetFillColor(255,255,0);
			$this->Cell($x-39, $h, str_pad($f["ID"],4,"0",STR_PAD_LEFT)."-".utf8_encode($f["NM"]), 0, false, 'L', true, false, 1);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 7);
			$this->setXY($x-34,$this->posY);
			$this->Cell(34, $h, strftime("Dia %d", strtotime($f["DT_NASC"]))." (".$f["IDADE_ANO"]." anos, Parabéns!)", 0, false, 'C', true);
		else:
			$this->SetFillColor(230,230,230);
			$this->Cell($x-27, $h, str_pad($f["ID"],4,"0",STR_PAD_LEFT)."-".utf8_encode($f["NM"]), 0, false, 'L', true, false, 1);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
			$this->setXY($x-22,$this->posY);
			$this->Cell(22, $h, strftime("%d/%m",strtotime($f["DT_NASC"]))." - ".$f["IDADE_HOJE"]." anos", 0, false, 'C', true);
		endif;
		
		$this->setCellPaddings(0,0,0,0);
		$this->SetFillColor(255,255,255);
		for ($i=0;$i<$this->cdates;$i++):
			$this->setXY($x,$this->posY);
			$this->Cell($this->xq, $h+4, "", $this->stLine3, false, 'C', true);
			$x+=$this->xq;
		endfor;
		$this->posY+=$h;

		$x = $this->leftMin;
		$h = 3;
		$this->posY++;
		$this->setCellPaddings(1,0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
		$this->setXY(12.5,$this->posY);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(80,80,80);
		$this->Cell($x-50.5, $h, utf8_encode($f["DS_CARGO"]), 0, false, 'L', true);
		$this->setXY($x-36,$this->posY);
		$this->Cell(35, $h, trim($f["FONE_RES"]."   ".$f["FONE_CEL"]), 0, false, 'R', true);
		
		$this->posY+=($h+2);
		$this->Line(5, $this->posY, 205, $this->posY, $this->stLine3);
		$this->posY+=2;
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("FichasDeChamada_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$aM = explode(",",fRequest("m"));
if ( !isset($aM) || count($aM) == 0 ):
	exit("SELECIONE OS MESES QUE DESEJA IMPRIMIR AS FICHAS DE CHAMADA!");
endif;
$u = fRequest("u");
$aU = explode(",",$u);
if ( !isset($aU) || count($aU) == 0 ):
	exit("SELECIONE AS UNIDADES QUE DESEJA IMPRIMIR AS FICHAS DE CHAMADA!");
endif;
$pdf = new CHAMADA();

fConnDB();
$result = $GLOBALS['conn']->Execute("SELECT * FROM TAB_UNIDADE WHERE ID IN ($u) ORDER BY TP, IDADE" );
foreach ( $result as $ru => $f ):
	foreach( $aM as $m ):
		$arr = explode("-",$m);
		$arr[] = ($f["TP"] == "A" ? "I" : "D");
	
		$rm = $GLOBALS['conn']->Execute("
			SELECT e.DTHORA_EVENTO_INI
			  FROM CAD_EVENTOS e
	     LEFT JOIN RGR_CHAMADA c ON (c.ID_EVENTO = e.ID_EVENTO)
			 WHERE YEAR(e.DTHORA_EVENTO_INI) = ?
			   AND MONTH(e.DTHORA_EVENTO_INI) = ?
			   AND c.TP_GRUPO IN ('T',?)
		  ORDER BY 1
		", $arr );
		$pdf->addChamada( $rm, $f );
	endforeach;
endforeach;
$pdf->download();
exit;
?>