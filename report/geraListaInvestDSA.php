<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTAINVESTIDURADSA extends TCPDF {
	
	//lines styles
	public $finalHTML;

	private $stLine;
	private $stLine2;
	private $lineAlt;
	private $dsItemAtu;
	private $dsItemAnt;
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
		$this->SetTitle('Listagem de Investidura - DSA');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Investiduras, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->heightHeader = 25;
		$this->SetMargins(5, $this->heightHeader, 5);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
		$this->SetAutoPageBreak(true, 10);
		
        $this->titleColor = "#009999";
        $this->dsItemAnt = null;
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
		$this->Cell(185, 9, "Listagem de Pessoas por Item - DSA", 0, false, 'C', false, false, true, false, 'T', 'M');
		$this->setXY(20,15);
		$this->SetTextColor(80,80,80);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
		$this->Cell(185, 5, $GLOBALS['pattern']->getCDS(), 0, false, 'C', false, false, false, false, false, false, 'T', 'M');
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
        if ($this->dsItemAtu !== $this->dsItemAnt):
            if (!is_null($this->dsItemAnt)):
                $this->writeGroupTable();
            endif;
            $this->seq = 0;
            $this->add("
                <table cellpadding=\"3\" border=\"0\" cellspacing=\"0\">
                    <tr>
                        <td width=\"100%\" colspan=\"2\" style=\"border:1px solid black;font-size:12px;font-weight:bold;color:#FFFFFF;background-color:".$this->titleColor."\">". $this->dsItemAtu ."</td>
                    </tr>
                    <tr>
                        <td width=\"7%\" style=\"border-left:1px solid black;border-bottom:1px solid black;text-align:center;font-weight:bold;color:#000000;background-color:#C2C2C2\">Seq.</td>
                        <td width=\"93%\" style=\"border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;color:#000000;font-weight:bold;background-color:#C2C2C2\">Nome</td>
                    </tr>
            ");
            
            $this->dsItemAnt = $this->dsItemAtu;
        endif;	    
	}
	
	public function addTableDetail($f){
        $color = "#ffffff";
		if ($this->lineAlt):
			$color = "#f0f0f0";
		endif;
	    $this->add("
            <tr>
                <td style=\"border-left:1px solid black;text-align:center;color:#000000;background-color:$color\">".(++$this->seq)."</td>
                <td style=\"border-left:1px solid black;border-right:1px solid black;color:#000000;background-color:$color\">".$f["NM"]."</td>
            </tr>	        
	    ");
	    $this->lineAlt = !$this->lineAlt;	    
	}

	public function addLine($f){
	    $this->dsItemAtu = $f["DS_ITEM"];
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
		$this->Output("ListagemInvestiduraDSA_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$pdf = new LISTAINVESTIDURADSA();
$pdf->newPage();

$request = fRequest("cmFiltro");
$filter = implode(",",array_map("fArrayStr", explode(",",$request)));
$innerJoinDA = ($request == "null" || empty($request) || empty($filter) ? "" : " AND ah.DT_AVALIACAO IN ($filter)");


$result = CONN::get()->Execute("
	SELECT DISTINCT
		1 AS ORD,
		ta.CD_ITEM_INTERNO AS X,
		ta.DS_ITEM,
		at.NM
	FROM CON_ATIVOS at
	INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = at.ID_CAD_PESSOA AND ah.DT_AVALIACAO IS NOT NULL AND ah.DT_INVESTIDURA IS NULL$innerJoinDA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
	  WHERE ta.TP_ITEM = 'CL'

    UNION ALL

	SELECT DISTINCT 
		2 AS ORD,
		ta.DS_ITEM AS X,
		ta.DS_ITEM,
		at.NM
	FROM CON_ATIVOS at
	INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = at.ID_CAD_PESSOA AND ah.DT_AVALIACAO IS NOT NULL AND ah.DT_INVESTIDURA IS NULL$innerJoinDA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
	  WHERE ta.TP_ITEM = 'ES'
	    AND SUBSTR(ta.CD_ITEM_INTERNO,1,2) <> 'ME'
    
    UNION ALL
    
	SELECT DISTINCT 
		3 AS ORD,
		ta.DS_ITEM AS X,
		ta.DS_ITEM,
		at.NM
	FROM CON_ATIVOS at
	INNER JOIN APR_HISTORICO ah ON (ah.ID_CAD_PESSOA = at.ID_CAD_PESSOA AND ah.DT_AVALIACAO IS NOT NULL AND ah.DT_INVESTIDURA IS NULL$innerJoinDA)
	INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
	INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
	  WHERE ta.TP_ITEM = 'ES'
	    AND SUBSTR(ta.CD_ITEM_INTERNO,1,2) = 'ME'

	ORDER BY 1, 2, 3, 4
");
foreach ( $result as $ra => $f ):
	$pdf->addLine($f);
endforeach;
$pdf->writeGroupTable();
$pdf->download();
exit;
?>