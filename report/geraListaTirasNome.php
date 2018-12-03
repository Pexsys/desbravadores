<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTACOMPRASNOMES extends TCPDF {
	
	//lines styles
	public $finalHTML;

	private $stLine;
	private $stLine2;
	private $lineAlt;
	private $dsItemAtu;
	private $dsNomeAnt;
	private $seq;
	private $titleColor;
	private $widthColumn;
	private $heightHeader;
	
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
		
		$this->SetTitle('Listagem de Compras - Tiras de Nome');
		$this->SetSubject(PATTERNS::getClubeDS(array("cl","nm")));
		$this->SetKeywords('Materiais, ' . str_replace(" ", ", ", PATTERNS::getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->heightHeader = 25;
		$this->SetMargins(5, $this->heightHeader, 5);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
		$this->SetAutoPageBreak(true, 10);
		
        $this->titleColor = "#33A0FF";
        $this->dsNomeAnt = null;
        $this->finalHTML = "";
		$this->lineAlt = false;
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
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 20);
		$this->Cell(185, 9, "Listagem de Compras - Tiras de Nome", 0, false, 'C', false, false, true, false, 'T', 'M');
		$this->setXY(20,15);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
		$this->Cell(185, 5, PATTERNS::getCDS(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
	}
	
	private function getFundo($fundo){
    if ( !is_null($fundo) && !empty($fundo) ):
      return $fundo == "BR" ? "BRANCO" : "CAQUI";
    endif;
    return "";
	}
	
	public function add($str){
	    $this->finalHTML .= $str;
	}
	
	public function writeGroupFooter(){
    if (!empty($this->finalHTML)):
          $this->add("
                  <tr>
                      <td style=\"border-top:1px solid black;\">&nbsp;</td>
                      <td style=\"border-top:1px solid black;\">&nbsp;</td>
                      <td style=\"border-top:1px solid black;\">&nbsp;</td>
                  </tr>
              </table>
          ");
      endif;
	}
	
	public function writeGroupTable(){
	    $this->writeGroupFooter();
	    
	    $this->startTransaction();
		$start_page = $this->getPage();
		$start_col  = $this->getColumn();
		$YInicial   = $this->getY();
		$this->writeHTML($this->finalHTML, false, false, false, true);
		
		if  ( (($start_col % 2) == 1) && $this->getNumPages() != $start_page ):
			$this->rollbackTransaction(true);
			$this->newPage();
			$this->selectColumn();
			$this->writeHTML($this->finalHTML, false, false, false, true);
			
		elseif ( $YInicial > $this->heightHeader && $this->getNumPages() != $start_page ):
			$this->rollbackTransaction(true);
			$this->selectColumn($start_col+1);
			$this->writeHTML($this->finalHTML, false, false, false, true);
			
		elseif ( $YInicial > $this->heightHeader && $start_col != $this->getColumn() ):
			$this->rollbackTransaction(true);
			$this->selectColumn($start_col+1);
			$this->writeHTML($this->finalHTML, false, false, false, true);
			
		else:
			$this->commitTransaction();
			
		endif;
        $this->lineAlt = false;
        $this->finalHTML = "";
	}
	
	public function addGroupHeader(){
        if ($this->dsItemAtu !== $this->dsNomeAnt):
            if (!is_null($this->dsNomeAnt)):
                $this->writeGroupTable();
            endif;
            $this->seq = 0;
            $this->add("
                <table cellpadding=\"3\" border=\"0\" cellspacing=\"0\">
                    <tr>
                        <td width=\"100%\" colspan=\"4\" style=\"border:1px solid black;font-size:12px;font-weight:bold;color:#FFFFFF;background-color:".$this->titleColor."\">". $this->dsItemAtu ."</td>
                    </tr>
                    <tr>
                        <td width=\"7%\" style=\"border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;color:#000000;background-color:#C2C2C2\">Qtd.</td>
                        <td width=\"23%\" style=\"border-left:1px solid black;border-bottom:1px solid black;color:#000000;font-weight:bold;background-color:#C2C2C2\">Bordar</td>
                        <td width=\"63%\" style=\"border-left:1px solid black;border-bottom:1px solid black;color:#000000;font-weight:bold;background-color:#C2C2C2\">Nome</td>
                        <td width=\"7%\" style=\"border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;text-align:center;font-weight:bold;color:#000000;background-color:#C2C2C2\">Seq.</td>
                    </tr>
            ");
            
            $this->dsNomeAnt = $this->dsItemAtu;
        endif;	    
	}

	private function descItem($f){
		$fundo = $this->getFundo($f["FUNDO"]);
	    if ($f["TP_ITEM"] == "ES"):
	    	return $f["CD_ITEM_INTERNO"] ." - ". $f["DS_ITEM"];
      else:
        return ($f["TP"] ." DE ". $f["DS"]. ($f["CMPL"] == "S" && $f["FG_IM"] =="N" ? " - ".$f["DS_ITEM"] : "") . ( !empty($fundo) ? " - FUNDO $fundo" : "" ));
      endif;
	}
	
	public function addTableDetail($f){
        $color = "#ffffff";
		if ($this->lineAlt):
			$color = "#f0f0f0";
		endif;
	    $this->add("
            <tr>
                <td style=\"border-left:1px solid black;text-align:center;color:#000000;background-color:$color\">".$f["QT_ITENS"]."</td>
                <td style=\"border-left:1px solid black;color:#000000;background-color:$color\">".$f["CM"]."</td>
                <td style=\"border-left:1px solid black;text-align:right;color:#000000;background-color:$color\">".$f["NM"]."</td>
                <td style=\"border-left:1px solid black;border-right:1px solid black;text-align:center;color:#000000;background-color:$color\">".(++$this->seq)."</td>
            </tr>	        
	    ");
	    $this->lineAlt = !$this->lineAlt;	    
	}

	public function addLine($f){
	    $this->dsItemAtu = $this->descItem($f);
      $this->addGroupHeader();
      $this->addTableDetail($f);
	}

	public function addLineSum($f){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
		$this->setCellPaddings(1,1,1,1);
		if ($this->lineAlt):
			$this->SetFillColor(240,240,240);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->setXY(5, $this->posY);
		$this->Cell(165, 6, $this->descItem($f), 0, false, 'L', true, false, 1);
		$this->setX(170);
		$this->Cell(35, 6, $f["QT_ITENS"], 0, false, 'C', true, false, 1);
		$this->posY+=6;
		$this->lineAlt = !$this->lineAlt;
	}

	public function addLineTotGrp($tp, $sum){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(127,163,195);
		$this->setCellPaddings(1,0,1,0);
		$this->setXY(5, $this->posY);
		$this->Cell(200, 6, "Quantidade [ $tp ]: $sum", 0, false, 'C', true);
		$this->posY+=8;
		$this->SetTextColor(0,0,0);
	}
	
	public function newPage($col=true) {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(5,25);
		$this->resetColumns();

		if ($col):
			$this->widthColumn = 98;
			$this->setEqualColumns(2, $this->widthColumn);
		else:
			$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
			$this->SetTextColor(255,255,255);
			$this->SetFillColor(51,160,255);
			$this->setCellPaddings(1,0,1,0);
			$this->setXY(5, 22);
			$this->Cell(165, 7, "Material", 0, false, 'L', true);
			$this->setXY(170, 22);
			$this->Cell(35, 7, "Quantidade", 0, false, 'C', true);
			$this->posY = 28;
			$this->SetTextColor(0,0,0);
		endif;
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemComprasTirasNome_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

function linhaGrupo($pdf,$ant,$sum){
	if (!empty($ant)):
		$pdf->startTransaction();
		$start_page = $pdf->getPage();
		$pdf->addLineTotGrp($ant, $sum);
		if  ($pdf->getNumPages() != $start_page):
			$pdf->rollbackTransaction(true);
			$pdf->newPage(false);
			$pdf->addLineTotGrp($ant, $sum);
		else:
			$pdf->commitTransaction();
		endif;
	endif;
}

$pdf = new LISTACOMPRASNOMES();
$pdf->newPage();

//QUERY DETALHE
$result = CONN::get()->Execute("
    SELECT cc.NM, cc.TP_ITEM, cc.CD, cc.DS_ITEM, cc.TP, cc.DS, cc.FUNDO, cc.FG_IM, cc.CD_AREA_INTERNO, cc.CD_ITEM_INTERNO, cc.CM, COUNT(*) AS QT_ITENS
    FROM CON_COMPRAS cc
    WHERE cc.FG_COMPRA = 'N'
      AND cc.FG_ENTREGUE = 'N'
	  AND cc.FG_PREVISAO = 'N'
	  AND cc.CD LIKE '05-05%'
    GROUP BY cc.NM, cc.TP_ITEM, cc.CD, cc.DS_ITEM, cc.TP, cc.DS, cc.FUNDO, cc.FG_IM, cc.CD_AREA_INTERNO, cc.CD_ITEM_INTERNO, cc.CM
    ORDER BY 3, 2, 9, 1, 4
");
foreach ( $result as $ra => $f ):
	$pdf->addLine($f);
endforeach;
$pdf->writeGroupTable();

//QUERY RESUMO
$pdf->newPage(false);
$result = CONN::get()->Execute("
	SELECT cc.TP_ITEM, cc.CD, cc.DS_ITEM, cc.TP, cc.DS, cc.FUNDO, cc.FG_IM, cc.CD_AREA_INTERNO, cc.CD_ITEM_INTERNO, COUNT(*) AS QT_ITENS
    FROM CON_COMPRAS cc
    WHERE cc.FG_COMPRA = 'N'
      AND cc.FG_ENTREGUE = 'N'
	  AND cc.FG_PREVISAO = 'N'
	  AND cc.CD LIKE '05-05%'
    GROUP BY cc.TP_ITEM, cc.CD, cc.DS_ITEM, cc.TP, cc.DS, cc.FUNDO, cc.FG_IM, cc.CD_AREA_INTERNO, cc.CD_ITEM_INTERNO
    ORDER BY 2, 8, 1, 3
");
$ant = "";
$sum = 0;

foreach ( $result as $ra => $f ):
	if ($ant != $f["TP"]):
		linhaGrupo($pdf,$ant,$sum);
		$sum = 0;
		$ant = $f["TP"];
	endif;
	$pdf->startTransaction();
	$start_page = $pdf->getPage();
	$pdf->addLineSum($f);
	if  ($pdf->getNumPages() != $start_page):
		$pdf->rollbackTransaction(true);
		$pdf->newPage(false);
		$pdf->addLineSum($f);
	else:
		$pdf->commitTransaction();
	endif;
	$sum += $f["QT_ITENS"];
endforeach;
linhaGrupo($pdf,$ant,$sum);
$pdf->download();
exit;
?>