<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class ESPCR extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $params;
	
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
		$this->SetTitle('Geração automática de capas de especialidades');
		$this->SetSubject('Clube Pioneiros');
		$this->SetKeywords('Desbravadores, Especialidades, Pioneiros, Capão Redondo');
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}

 	public function Header() {
	}
	
	public function Footer() {
		if (!empty($this->params[0])):
			$this->SetY(-20);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 8);
			$this->Cell(0, 10, fClubeID(), 0, false, 'C');
		endif;
	}
	
	public function addEspecialidade($codEsp,$params) {
		$this->params = $params;
		
		$result = $GLOBALS['conn']->Execute("
			SELECT ta.ID, ta.DS_ITEM, ta.CD_AREA_INTERNO, tm.NR_PG_ASS
			  FROM TAB_APRENDIZADO ta
		INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
			 WHERE ta.CD_ITEM_INTERNO = ?
			   AND ta.TP_ITEM = ?", Array( $codEsp, "ES" ) );
 
		if ($result->EOF):
			return;
		endif;

		//achar o nome e a area com select
		$nomeEsp = utf8_encode($result->fields['DS_ITEM']);
		$areaEsp = $result->fields['CD_AREA_INTERNO'];
		$pgAss = $result->fields['NR_PG_ASS'];

		$this->AddPage();
		$this->setXY(0,0);
		$this->Image("img/aprendizado/ES/$areaEsp/$codEsp.jpg", 32, 30, 150, 114, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->Ln(130);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'BI', 25);
		$this->Cell(0, 0, "$nomeEsp", 0, false, 'C', false, false, 1, false, 'C', 'C');

		$this->Ln(5);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 13);
		$this->Cell(0, 0, "$codEsp - #$pgAss", 0, false, 'C');
		
		if (!empty($this->params[0])):
			$barCODE = mb_strtoupper("PE". fStrZero(base_convert($result->fields["ID"],10,36),2) . fStrZero(base_convert($this->params[0],10,36),3));
			$this->write1DBarcode($barCODE, 'C39', 73, 178, '', 17, 0.4, $this->stLine3, 'N');
		endif;
		
		$tbTop = 205;
		$this->setY($tbTop);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 17);
		$this->Cell(0, 0, $this->params[1], 0, false, 'C', false, false, 1, false, 'C', 'C');
		
		$tbTop += 5;		
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->SetFillColor(50,50,50);
		$this->SetTextColor(255,255,255);
		$this->RoundedRect(30, $tbTop, 160, 6, 2.5, '1001', 'FD', $this->stLine2);
		$this->setXY(35, $tbTop+1);
		$this->Cell(150, 4, "Avaliação Interna", '', 1, 'C', 1, '', 0, false, 'T', 'C');
		$tbTop += 6;
		$this->SetFillColor(120,120,120);
		$this->setXY(30, $tbTop);
		$this->Cell(32, 5, "Data", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(62, $tbTop);
		$this->Cell(32, 5, "Trabalho (3)", 'T', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(94, $tbTop);
		$this->Cell(32, 5, "Aula (1)", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(126, $tbTop);
		$this->Cell(32, 5, "Prova (6)", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(158, $tbTop);
		$this->Cell(32, 5, "Total (10)", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$tbTop += 5;
		$this->SetFillColor(255,255,255);
		$this->setXY(30, $tbTop);
		$this->Cell(32, 12, "", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(62, $tbTop);
		$this->Cell(32, 12, "", 'T', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(94, $tbTop);
		$this->Cell(32, 12, "", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(126, $tbTop);
		$this->Cell(32, 12, "", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(158, $tbTop);
		$this->Cell(32, 12, "", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$tbTop += 12;
		$this->SetFillColor(120,120,120);
		$this->setXY(30, $tbTop);
		$this->Cell(112, 5, "Nome do Instrutor", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(142, $tbTop);
		$this->Cell(48, 5, "Assinatura", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$tbTop += 5;
		$this->SetFillColor(255,255,255);
		$this->RoundedRect(30, $tbTop, 112, 12, 2.5, '0010', 'D', $this->stLine2);
		$this->RoundedRect(142, $tbTop, 48, 12, 2.5, '0100', 'D', $this->stLine2);
		$this->Line(62, $tbTop-22, 62, $tbTop-5, $this->stLine);

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$tbTop += 14;
		$this->SetFillColor(50,50,50);
		$this->RoundedRect(30, $tbTop, 112, 6, 2.5, '0001', 'FD', $this->stLine2);
		$this->setXY(35, $tbTop+1);
		$this->Cell(102, 4, "Avaliação Regional", '', 1, 'C', 1, '', 0, false, 'T', 'C');

		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
		$this->RoundedRect(142, $tbTop, 48, 6, 2.5, '1000', 'FD', $this->stLine2);
		$this->setXY(147, $tbTop+1);
		$this->Cell(38, 4, "Investidura", '', 1, 'C', 1, '', 0, false, 'T', 'C');
		$tbTop += 6;
		$this->SetFillColor(120,120,120);
		$this->setXY(30, $tbTop);
		$this->Cell(32, 5, "Data", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(62, $tbTop);
		$this->Cell(80, 5, "Carimbo / Assinatura", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(142, $tbTop);
		$this->Cell(48, 5, "Data", 'TR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$tbTop += 5;
		$this->SetFillColor(255,255,255);
		$this->RoundedRect(30, $tbTop, 32, 10, 2.5, '0010', 'D', $this->stLine2);
		$this->setXY(62, $tbTop);
		$this->Cell(80, 10, "", 'TLB', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->RoundedRect(142, $tbTop, 48, 10, 2.5, '0100', 'D', $this->stLine2);
		$this->Line(142, $tbTop-11, 142, $tbTop+10, $this->stLine);
	}

	public function download() {
		$this->lastPage();
		$this->Output("CapasDeEspecialidades_".date('Y-m-d_H:i:s').".pdf", 'I');
	}
}

$nome = fRequest("nome");
if ( !isset($nome) || empty($nome) || $nome == "null" ):
	echo "NOME INVÁLIDO!";
	exit;
endif;
$list = fRequest("list");
if ( !isset($list) || strlen($list) == 0 || stristr($list, "indispon") ):
	echo "SELECIONE NA TABELA, AS CAPAS DAS ESPECIALIDADES QUE DESEJA IMPRIMIR!";
	exit;
endif;
$list = explode(",",$list);
if ( count($list) == 0 ):
	echo "SELECIONE NA TABELA, AS CAPAS DAS ESPECIALIDADES QUE DESEJA IMPRIMIR!";
	exit;
endif;

fConnDB();

$pdf = new ESPCR();

$arrNome = array();
if ($nome == "ALL"):
	$result = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM CON_ATIVOS 
		 ORDER BY NM");
	foreach ($result as $k => $line):
		$arrNome[] = $line["ID"] ."|". utf8_encode($line["NM"]);
	endforeach;
else:
	$arrNome = explode(",",$nome);
	if ( !isset($arrNome) || count($arrNome) == 0 ):
		$arrNome[] = $nome;
	endif;
endif;

foreach( $arrNome as $nm ):
	foreach ( $list as $value ):
		$arr = explode("|",$nm);
		$pdf->addEspecialidade( $value, $arr );
	endforeach;
endforeach;
$pdf->download();
exit;
?>