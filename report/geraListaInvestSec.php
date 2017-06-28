<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAINVESTIDURASEC extends TCPDF {
	
	//lines styles
	public $finalHTML;

	private $stLine;
	private $stLine2;
	private $lineAlt;
	private $dsNomeAtu;
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
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetTitle('Listagem de Investidura - Secretaria do Clube');
		$this->SetSubject('Clube Pioneiros');
		$this->SetKeywords('Desbravadores, Especialidades, Pioneiros, Capão Redondo');
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->heightHeader = 25;
		$this->SetMargins(5, $this->heightHeader, 5);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
		$this->SetAutoPageBreak(true, 10);
		
        $this->titleColor = "#FF9933";
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
		$this->Cell(185, 9, "Listagem de Itens por Pessoa - Investidura", 0, false, 'C', false, false, false, false, 'T', 'M');
		$this->setXY(20,15);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
		$this->Cell(185, 5, fClubeID(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
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
        if ($this->dsNomeAtu !== $this->dsNomeAnt):
            if (!is_null($this->dsNomeAnt)):
                $this->writeGroupTable();
            endif;
            $this->seq = 0;
            $this->add("
                <table cellpadding=\"3\" border=\"0\" cellspacing=\"0\">
                    <tr>
                        <td width=\"100%\" colspan=\"2\" style=\"border:1px solid black;font-size:12px;font-weight:bold;color:#FFFFFF;background-color:".$this->titleColor."\">". $this->dsNomeAtu ."</td>
                    </tr>
                    <tr>
                        <td width=\"7%\" style=\"border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;color:#000000;background-color:#C2C2C2\">Qtd.</td>
                        <td width=\"86%\" style=\"border-left:1px solid black;border-bottom:1px solid black;color:#000000;font-weight:bold;background-color:#C2C2C2\">Nome</td>
                        <td width=\"7%\" style=\"border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;text-align:center;font-weight:bold;color:#000000;background-color:#C2C2C2\">Seq.</td>
                    </tr>
            ");
            
            $this->dsNomeAnt = $this->dsNomeAtu;
        endif;	    
	}
	
	public function addTableDetail($f){
        $color = "#ffffff";
		if ($this->lineAlt):
			$color = "#f0f0f0";
		endif;
	    $fundo = $this->getFundo($f["FUNDO"]);
	    $desc = utf8_encode($f["TP"] ." DE ". $f["DS"]. ($f["CMPL"] == "S" && $f["FG_IM"] =="N" ? " - ".$f["DS_ITEM"] : "") . ( !empty($fundo) ? " - FUNDO $fundo" : "" ));
	    $this->add("
            <tr>
                <td style=\"border-left:1px solid black;text-align:center;color:#000000;background-color:$color\">".$f["QT_ITENS"]."</td>
                <td style=\"border-left:1px solid black;color:#000000;background-color:$color\">$desc</td>
                <td style=\"border-left:1px solid black;border-right:1px solid black;text-align:center;color:#000000;background-color:$color\">".(++$this->seq)."</td>
            </tr>	        
	    ");
	    $this->lineAlt = !$this->lineAlt;	    
	}

	public function addLine($f){
	    $this->dsNomeAtu = utf8_encode($f["NM"]);
        $this->addGroupHeader();
        $this->addTableDetail($f);
	}
	
	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(5,25);
		
		$this->resetColumns();
		$this->widthColumn = 98;
		$this->setEqualColumns(2, $this->widthColumn);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemInvestiduraSecretaria_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$pdf = new LISTAINVESTIDURASEC();
$pdf->newPage();

fConnDB();

$result = $GLOBALS['conn']->Execute("
    SELECT NM, TP_ITEM, CD, DS_ITEM, TP, DS, FUNDO, FG_IM, COUNT(*) AS QT_ITENS
    FROM CON_COMPRAS
    WHERE FG_IM = 'N'
      AND FG_COMPRA = 'S'
    GROUP BY NM, TP_ITEM, CD, DS_ITEM, TP, DS, FUNDO, FG_IM
    
    UNION ALL
    
    SELECT NM, TP_ITEM, CD, DS_ITEM, TP, DS, FUNDO, FG_IM, COUNT(*) AS QT_ITENS
    FROM CON_COMPRAS
    WHERE FG_IM = 'S'
      AND FG_COMPRA = 'S'
    GROUP BY NM, TP_ITEM, CD, DS_ITEM, TP, DS, FUNDO, FG_IM
    
    ORDER BY 1, 2, 3, 4
");
foreach ( $result as $ra => $f ):
	$pdf->addLine($f);
endforeach;
$pdf->writeGroupTable();
$pdf->download();
exit;
?>