<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class ESPCR extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $stLine3;
	private $line;
	private $top;
	
	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$this->stLine = array(
		    'position' => '',
		    'align' => 'C',
		    'stretch' => false,
		    'fitwidth' => true,
		    'cellfitalign' => '',
		    'border' => FALSE,
		    'hpadding' => 'auto',
		    'vpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255),
		    'text' => true,
		    'font' => 'helvetica',
		    'fontsize' => 10,
		    'stretchtext' => 0
		);
		$this->stLine2 = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => '2,4', 'color' => array(110, 110, 110));
		$this->stLine3 = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => '0', 'color' => array(0, 0, 0));
		
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Ricardo J. Cesar');
		$this->SetTitle('Geração automática de registro de histórico');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Histórico, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->SetTopMargin(4);
		$this->SetFooterMargin(0);
		$this->SetLeftMargin(4);
		$this->SetRightMargin(4);
		$this->SetHeaderMargin(0);
		$this->SetFooterMargin(0);
	}
	
	public function setLine($line){
	    $this->line = $line;
	}

 	public function Header() {
  		$this->setXY(0,0);
 		$this->Image("img/iasd-full.jpg", 5, 7, 30, 28, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
 		//$this->Image("img/logod1.jpg", 35, 9, 23, 23, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->Image("img/logo.jpg", 35, 8, 22, 27, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
 		
 		$this->Line(165, 7, 165, 35, $this->stLine2);

 		$this->setXY(167,7);
 		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
 		$this->Cell(44, 4, "Distrito de ".$GLOBALS['pattern']->getClubeDS( array("dst") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(167,11);
 		$this->Cell(44, 4, $GLOBALS['pattern']->getClubeDS( array("add") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(167,15);
 		$this->Cell(44, 4, $GLOBALS['pattern']->getClubeDS( array("dst") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(167,19);
 		$this->Cell(44, 4, $GLOBALS['pattern']->getClubeDS( array("cid") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(167,23);
 		$this->Cell(44, 4, $GLOBALS['pattern']->getClubeDS( array("cep") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(167,27);
 		$this->Cell(44, 4, $GLOBALS['pattern']->getClubeDS( array("cnpj") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(167,31);
 		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
 		$this->SetTextColor(0,128,128);
 		$this->Cell(44, 4, $GLOBALS['pattern']->getClubeDS( array("as") ), 0, false, 'L', false, false, false, false, 'T', 'M');

 		$this->setXY(5,40);
 		$this->SetTextColor(0,0,0);
 		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 13);
 		$this->Cell(200, 8, "REGISTRO DE ATIVIDADES" . ($this->getNumPages() > 1 ? " (Continuação)" : ""), 0, false, 'C', false, false, false, false, 'T', 'M');
 		$this->top = 48;
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

	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);

		$this->top += 2;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 7);
		$this->SetFillColor(0,0,0);
		$this->SetTextColor(255,255,255);
		$this->RoundedRect(10, $this->top, 190, 6, 1, '1001', 'FD', $this->stLine);
		$this->setXY(11, $this->top+1);
		$this->Cell(15, 4, "Código", '', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(29, $this->top+1);
		$this->Cell(145, 4, "Nome Completo", 'R', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(180, $this->top+1);
		$this->Cell(19, 4, "Nascimento", '', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->top += 6;
		
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		$this->setXY(11, $this->top);
		$this->Cell(15, 7, $this->line['ID'], 'R', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(29, $this->top);
		$this->Cell(145, 7, $this->line['NM'], 'R', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(179, $this->top);
		$this->Cell(20, 7, strftime("%d/%m/%Y",strtotime($this->line["DT_NASC"])), '', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->RoundedRect(10, $this->top, 190, 7, 1, '0110', 'D', $this->stLine);
        $this->top += 10;
	}

	private function linhaAprendizado($bg,$fa){
		if ($bg):
			$this->SetFillColor(255,255,255);
		else:
			$this->SetFillColor(225,225,225);
		endif;
		$this->setXY(10, $this->top);
		$this->Cell(7, 4, $fa["TP_ITEM"], 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(17, $this->top);
		$this->Cell(15, 4, ( $fa["TP_ITEM"] == "CL" ? $fa["CD_AREA_INTERNO"] : $fa["CD_ITEM_INTERNO"] ), 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(32, $this->top);
		$this->Cell(108, 4, " ".$fa["DS_ITEM"], 'L', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(140, $this->top);
		$this->Cell(15, 4, ( is_null($fa["DT_INICIO"]) ? "--" : strftime("%d/%m/%Y",strtotime($fa["DT_INICIO"])) ), 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(155, $this->top);
		$this->Cell(15, 4, ( is_null($fa["DT_CONCLUSAO"]) ? "--" : strftime("%d/%m/%Y",strtotime($fa["DT_CONCLUSAO"])) ), 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->top);
		$this->Cell(15, 4, ( is_null($fa["DT_AVALIACAO"]) ? "--" : strftime("%d/%m/%Y",strtotime($fa["DT_AVALIACAO"])) ), 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(185, $this->top);
		$this->Cell(15, 4, ( is_null($fa["DT_INVESTIDURA"]) ? "--" : strftime("%d/%m/%Y",strtotime($fa["DT_INVESTIDURA"])) ), 'LR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->top += 4;
		return !$bg;
	}

	private function grupoAprendizado(){
		$this->SetFillColor(85,85,85);
		$this->SetTextColor(255,255,255);
		$this->setXY(10, $this->top+1);
		$this->Cell(190, 5, "APRENDIZADO", '1', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->top += 6;
		$this->SetFillColor(170,170,170);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 7);
		$this->setXY(10, $this->top);
		$this->Cell(7, 5, "Tipo", 'LB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(17, $this->top);
		$this->Cell(15, 5, "Código", 'LB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(32, $this->top);
		$this->Cell(108, 5, " Descrição", 'LB', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(140, $this->top);
		$this->Cell(15, 5, "Início", 'LB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(155, $this->top);
		$this->Cell(15, 5, "Fim", 'LB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->top);
		$this->Cell(15, 5, "Avaliação", 'LB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(185, $this->top);
		$this->Cell(15, 5, "Investidura", 'LRB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->top += 5;
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		return true;
	}

	private function grupoEvento(){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetFillColor(85,85,85);
		$this->SetTextColor(255,255,255);
		$this->setXY(10, $this->top);
		$this->Cell(190, 5, "EVENTOS", '1', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->top += 5;
		
		$this->SetFillColor(170,170,170);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 7);
		$this->setXY(10, $this->top);
		$this->Cell(55, 5, " Descrição", 'LB', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(65, $this->top);
		$this->Cell(55, 5, " Tema", 'LB', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(120, $this->top);
		$this->Cell(50, 5, " Organização", 'LB', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->top);
		$this->Cell(15, 5, "Início", 'LB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(185, $this->top);
		$this->Cell(15, 5, "Fim", 'LRB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->top += 5;

		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
		return true;
	}

	private function linhaEvento($bg,$fe){
		if ($bg):
			$this->SetFillColor(255,255,255);
		else:
			$this->SetFillColor(225,225,225);
		endif;
		$this->setXY(10, $this->top);
		$this->Cell(55, 4, " ".$fe["DS"], 'L', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(65, $this->top);
		$this->Cell(55, 4, " ".$fe["DS_TEMA"], 'L', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(120, $this->top);
		$this->Cell(50, 4, " ".$fe["DS_ORG"], 'L', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->setXY(170, $this->top);
		$this->Cell(15, 4, ( is_null($fe["DH_S"]) ? "--" : strftime("%d/%m/%Y",strtotime($fe["DH_S"])) ), 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(185, $this->top);
		$this->Cell(15, 4, ( is_null($fe["DH_R"]) ? "--" : strftime("%d/%m/%Y",strtotime($fe["DH_R"])) ), 'LR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->top += 4;
		return !$bg;
	}
	
	public function add() {
	    $this->newPage();
        
        $rYear = $GLOBALS['conn']->Execute("
            SELECT YEAR(es.DH_S) as YEAR_INDEX
            FROM EVE_SAIDA_MEMBRO esp
            INNER JOIN EVE_SAIDA es ON (es.ID = esp.ID_EVE_SAIDA)
            WHERE esp.ID_CAD_MEMBRO = ? 
            UNION 
            SELECT YEAR(DT_INICIO) as YEAR_INDEX
              FROM APR_HISTORICO 
             WHERE ID_CAD_PESSOA = ?
            ORDER BY 1
        ", array($this->line['ID'], $this->line['ID_CAD_PESSOA']) );
        
        foreach ($rYear as $yK => $f):
            $o = $GLOBALS['conn']->Execute("
				SELECT tu.DS, IF(cp.TP_SEXO='F',tc.DSF,tc.DSM) AS DS_CARGO
				FROM CAD_ATIVOS ca
				INNER JOIN CAD_MEMBRO cm ON (cm.ID = ca.ID_CAD_MEMBRO)
				INNER JOIN CAD_PESSOA cp ON (cp.ID = cm.ID_CAD_PESSOA)
				INNER JOIN TAB_UNIDADE tu ON (tu.ID = ca.ID_UNIDADE)
				INNER JOIN TAB_CARGO tc ON (tc.CD = ca.CD_CARGO)
				WHERE cm.ID = ?
				AND ca.NR_ANO = ?
            ", array($this->line['ID_CAD_PESSOA'],$f["YEAR_INDEX"]));
            
     		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
    		$this->SetFillColor(0,0,0);
    		$this->SetTextColor(255,255,255);
    		$this->RoundedRect(10, $this->top, 190, 6, 1, '1001', 'FD', $this->stLine);
    		$this->setXY(11, $this->top+1);
    		$this->Cell(50, 5, "ANO: ".$f["YEAR_INDEX"], '', 1, 'L', 1, '', 0, false, 'T', 'C');
    		$this->setXY(80, $this->top+1);
    		$this->Cell(50, 5, "UNIDADE: ".$o->fields["DS"], '', 1, 'C', 1, '', 0, false, 'T', 'C');
    		$this->setXY(149, $this->top+1);
    		$this->Cell(50, 5, "CARGO: ".$o->fields["DS_CARGO"], '', 1, 'R', 1, '', 0, false, 'T', 'C');
    		$this->top += 5;
    		
    		$aprend = $GLOBALS['conn']->Execute("
                SELECT ta.TP_ITEM, ta.DS_ITEM, ta.CD_ITEM_INTERNO, ta.CD_AREA_INTERNO,
                       ah.DT_INICIO, ah.DT_CONCLUSAO, ah.DT_AVALIACAO, ah.DT_INVESTIDURA
                  FROM APR_HISTORICO ah
            INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = ah.ID_TAB_APREND)
                 WHERE ah.ID_CAD_PESSOA = ?
                   AND YEAR(ah.DT_INICIO) = ?
                ORDER BY ah.DT_INICIO, ta.TP_ITEM, ta.CD_AREA_INTERNO DESC, ta.DS_ITEM
            ", array($this->line['ID_CAD_PESSOA'], $f["YEAR_INDEX"]));
            if (!$aprend->EOF):
				
				//AGRUPADOR DE APRENDIZADO
				$this->startTransaction();
				$start_page = $this->getPage();
				$bg = $this->grupoAprendizado();
				if  ($this->getNumPages() != $start_page):
					$this->rollbackTransaction(true);
					$this->newPage();
					$bg = $this->grupoAprendizado();
				else:
					$this->commitTransaction();
				endif;

				//DETALHE DO APRENDIZADO
				foreach($aprend as $kaprend => $fa):
					$this->startTransaction();
					$start_page = $this->getPage();
					$bg = $this->linhaAprendizado($bg,$fa);
					if  ($this->getNumPages() != $start_page):
						$this->rollbackTransaction(true);
						$this->newPage();
						$bg = $this->grupoAprendizado();
						$bg = $this->linhaAprendizado($bg,$fa);
					else:
						$this->commitTransaction();
					endif;
                endforeach;
        	endif;
        	
        	//EVENTOS
            $events = $GLOBALS['conn']->Execute("
            SELECT es.DS, es.DS_TEMA, es.DS_ORG, es.DH_S, es.DH_R
            FROM EVE_SAIDA_MEMBRO esp
            INNER JOIN EVE_SAIDA es on (es.ID = esp.ID_EVE_SAIDA)
            WHERE esp.ID_CAD_MEMBRO = ? 
              AND YEAR(es.DH_S) = ?
            ORDER BY es.DH_S
            ", array($this->line['ID'], $f["YEAR_INDEX"]));
			if (!$events->EOF):

				//AGRUPADOR DE EVENTOS
				$this->startTransaction();
				$start_page = $this->getPage();
				$bg = $this->grupoEvento();
				if  ($this->getNumPages() != $start_page):
					$this->rollbackTransaction(true);
					$this->newPage();
					$bg = $this->grupoEvento();
				else:
					$this->commitTransaction();
				endif;

				foreach($events as $kevents => $fe):
					$this->startTransaction();
					$start_page = $this->getPage();
					$bg = $this->linhaEvento($bg,$fe);
					if  ($this->getNumPages() != $start_page):
						$this->rollbackTransaction(true);
						$this->newPage();
						$bg = $this->grupoEvento();
						$bg = $this->linhaEvento($bg,$fe);
					else:
						$this->commitTransaction();
					endif;
					
                endforeach;
        	endif;
            $this->Line(10, $this->top, 200, $this->top);
    		$this->top += 2;
        endforeach;
	}

	public function download() {
		$this->lastPage();
		$this->Output("RegistroHistorico_".date('Y-m-d_H:i:s').".pdf", 'I');
	}
}

$filter = fRequest("filter");
if ( !isset($filter) || empty($filter) || strlen($filter) == 0 ):
	exit("PESSOA N&Atilde;O ENCONTRADA!");
endif;
fConnDB();

$pdf = new ESPCR();
$result = $GLOBALS['conn']->Execute("
	SELECT cm.ID, cm.ID_CAD_PESSOA, cp.NM, cp.DT_NASC
		FROM CAD_MEMBRO cm
  INNER JOIN CAD_PESSOA cp ON (cp.ID = cm.ID_CAD_PESSOA)
    WHERE cm.ID IN ($filter)
    ORDER BY cp.NM
");
if (!$result->EOF):
	foreach ($result as $k => $line):
	   $pdf->setLine( $line );
	   $pdf->add();
	endforeach;
	$pdf->download();
endif;
exit;
?>