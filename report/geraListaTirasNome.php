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
	private $titleColor;
	private $widthColumn;
	private $heightHeader;
  private $fundoAtu;
  private $fundoAnt;
	private $sum;
	
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
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
		$this->SetAutoPageBreak(true, 10);
		
    $this->dsNomeAnt = null;
    $this->sum = 0;
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
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 12);
		$this->Cell(185, 5, PATTERNS::getCDS(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
	}
	
	public function add($str){
	    $this->finalHTML .= $str;
	}
	
	public function writeGroupFooter(){
    if (!empty($this->finalHTML)):
        $cb = $this->fundoAnt == "BRANCO" ? "#FFFFFF" : "#DFC38A";
        $this->add("
              <tr>
                  <td colspan=\"2\" style=\"border:1px solid black;text-align:center;font-size:10px;font-weight:bold;color:#000000;background-color:$cb\">".$this->dsNomeAnt. ": ". $this->sum ."</td>
              </tr>
              <tr>
                  <td colspan=\"2\">&nbsp;</td>
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
        $this->sum = 0;
	}
	
	public function addGroupHeader($f){
        if ($this->dsItemAtu !== $this->dsNomeAnt):
            if (!is_null($this->dsNomeAnt)):
                $this->writeGroupTable();
            endif;
            $cb = $this->fundoAtu == "BRANCO" ? "#FFFFFF" : "#DFC38A";
            $this->add("
                <table cellpadding=\"3\" border=\"0\" cellspacing=\"0\">
                    <tr>
                        <td width=\"100%\" colspan=\"2\" style=\"border:1px solid black;font-size:16px;text-align:center;font-weight:bold;color:#000000;background-color:$cb\">". $this->dsItemAtu ."</td>
                    </tr>
                    <tr>
                        <td width=\"15%\" style=\"border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;color:#000000;background-color:#C2C2C2\">Qtd.</td>
                        <td width=\"85%\" style=\"border-left:1px solid black;border-bottom:1px solid black;color:#000000;border-right:1px solid black;font-weight:bold;background-color:#C2C2C2\">Bordar</td>
                    </tr>
            ");
            
            $this->dsNomeAnt = $this->dsItemAtu;
            $this->fundoAnt = $this->fundoAtu;
        endif;
	}

	private function descItem($f){
    $this->fundoAtu = ($f["FUNDO"] == "BR") ? "BRANCO" : "CAQUI";
    if ($f["TP_ITEM"] == "ES"):
      return $f["CD_ITEM_INTERNO"] ." - ". $f["DS_ITEM"];
    else:
        return $f["TP"] ." DE ". $f["DS"]. ($f["CMPL"] == "S" && $f["FG_IM"] =="N" ? " - ".$f["DS_ITEM"] : "") . " - FUNDO ".$this->fundoAtu;
    endif;
	}
	
	public function addTableDetail($f){
    $color = "#ffffff";
		if ($this->lineAlt):
			$color = "#f0f0f0";
		endif;
	    $this->add("
            <tr>
                <td style=\"border-left:1px solid black;font-size:14px;text-align:center;color:#000000;background-color:$color\">".$f["QT_ITENS"]."</td>
                <td style=\"border-left:1px solid black;border-right:1px solid black;font-size:14px;color:#000000;background-color:$color\">".$f["CM"]."</td>
            </tr>	        
	    ");
	    $this->sum += $f["QT_ITENS"];
	    $this->lineAlt = !$this->lineAlt;
	}

	public function addLine($f){
	  $this->dsItemAtu = $this->descItem($f);
    $this->addGroupHeader($f);
    $this->addTableDetail($f);
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

$pdf = new LISTACOMPRASNOMES();
$pdf->newPage();

//QUERY DETALHE
$result = CONN::get()->execute("
    SELECT cc.CM, cc.TP_ITEM, cc.CD, cc.DS_ITEM, cc.TP, cc.DS, cc.FUNDO, cc.FG_IM, cc.CD_AREA_INTERNO, cc.CD_ITEM_INTERNO, COUNT(*) AS QT_ITENS
    FROM CON_COMPRAS cc
    WHERE cc.FG_COMPRA = 'N'
      AND cc.FG_ENTREGUE = 'N'
	  AND cc.FG_PREVISAO = 'N'
	  AND cc.CD LIKE '05-05%'
    GROUP BY cc.CM, cc.TP_ITEM, cc.CD, cc.DS_ITEM, cc.TP, cc.DS, cc.FUNDO, cc.FG_IM, cc.CD_AREA_INTERNO, cc.CD_ITEM_INTERNO
    ORDER BY 3, 2, 9, 1, 4
");
foreach ( $result as $ra => $f ):
	$pdf->addLine($f);
endforeach;
$pdf->writeGroupTable();
$pdf->download();
exit;
?>
