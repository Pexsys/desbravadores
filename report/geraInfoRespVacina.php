<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class TERMOINFORMACAO extends TCPDF {
	
	//lines styles
	public $stLine;
	public $stLine2;
	public $stLine3;
	public $lineAlt;
	public $posY;
	
	function __construct() {
		parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$this->stLine = array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->stLine2 = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => '2,4', 'color' => array(110, 110, 110));
		$this->stLine3 = array('width' => 0.3, 'cap' => 'round', 'join' => 'round', 'dash' => '0', 'color' => array(0, 0, 0));

		$this->SetCreator(PDF_CREATOR);
		
		$this->SetTitle('Termo de Informação e Responsabilidade');
		$this->SetSubject(PATTERNS::getClubeDS(array("cl","nm")));
		$this->SetKeywords('Responsabilidade, Informação, ' . str_replace(" ", ", ", PATTERNS::getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}

	public function Footer() {
		$this->SetTextColor(90,90,90);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 6);
		$this->SetY(-9);
		$this->Cell(40, 3, "Página ". $this->getAliasNumPage() ." de ". $this->getAliasNbPages(), 0, false, 'L');
		
		$this->Image("img/logod1.jpg", 160, 264, 22, 22, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->Image("img/logo.jpg", 183, 263, 20, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	}
	
 	public function Header() {
 		$this->setXY(0,0);
 		$this->Image("img/iasd-full.jpg", 10, 7, 32, 30, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
 		
 		$this->Line(161, 7, 161, 42, $this->stLine2);

 		$this->setXY(163,7);
 		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
 		$this->Cell(44, 5, "Distrito de ".PATTERNS::getClubeDS( array("dst") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,12);
 		$this->Cell(44, 5, PATTERNS::getClubeDS( array("add") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,17);
 		$this->Cell(44, 5, PATTERNS::getClubeDS( array("dst") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,22);
 		$this->Cell(44, 5, PATTERNS::getClubeDS( array("cid") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,27);
 		$this->Cell(44, 5, PATTERNS::getClubeDS( array("cep") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,32);
 		$this->Cell(44, 5, PATTERNS::getClubeDS( array("cnpj") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,37);
 		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
 		$this->SetTextColor(0,128,128);
 		$this->Cell(44, 5, PATTERNS::getClubeDS( array("as") ), 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->SetTextColor(0,0,0);
 		$this->posY = 48;
 	}

	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("TermoResponsabilidade_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$filter = fRequest("filter");
if ( !isset($filter) || empty($filter) || is_null($filter) || strlen($filter) == 0 ):
	exit("MENOR N&Atilde;O ENCONTRADA!");
endif;

$result = CONN::get()->Execute("
	SELECT * FROM CON_ATIVOS ca
    WHERE ca.ID_MEMBRO IN ($filter)
	ORDER BY ca.NM
");
if (!$result->EOF):
    $pdf = new TERMOINFORMACAO();
    
    foreach ($result as $k => $f):
		$pdf->newPage();

		$pdf->setXY(5,65);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont(PDF_FONT_NAME_MAIN, 'B', 14);
		$pdf->Cell(200, 8, "Termo de Isenção de Responsabilidade", 0, false, 'C', false, false, false, false, 'T', 'M');

		$oa = ($f["TP_SEXO"] == "F" ? "a" : "o");
		$meuminha = ($f["TP_SEXO"] == "F" ? "minha" : "meu");

		$dtS = strtotime($f["DH_S"]);
		$dtR = strtotime($f["DH_R"]);
		$linhaASS = trim($f["NM_RESP"]).", ".$f["NR_DOC_RESP"].", CPF ".fCPF($f["NR_CPF_RESP"]). (!empty($f["FONE_CEL_RESP"]) ? ", Fone ".$f["FONE_CEL_RESP"] : "");

		$pdf->SetTextColor(0,0,0);
		$pdf->SetY(85);
		$pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
		$html = "<p align=\"justify\">Eu, ". trim($f["NM_RESP"]) .", declaro através deste termo, que estou ciente de que $meuminha dependente, $oa menor ".
				"<b><u>". trim($f["NM"])."</u></b>, ".$f["NR_DOC"].", está expost$oa aos riscos pela falta de imunização de vacinas oferecidas pelos orgãos de saúde competentes.
				<br/>
				<br/>
				Entendo plenamente que o Clube Pioneiros, para que possa realizar e completar sua programação educacional, necessita participar de eventos, realizar saídas e atividades, dentro ou fora da localidade oficial de reuniões,
				seja em ambiente urbano, rural, ao ar livre ou meio a natureza, e assim sendo, <b>ISENTO</b> e abdico responsabilizar, em qualquer instância judicial, o(os) responsável(eis) do Clube Pioneiros em todos os níveis,
				bem como a Igreja Adventista do Sétimo Dia que a falta de imunização possa trazer para a saúde de $meuminha dependente acima citad$oa, antes, durante e após os eventos e saídas do clube.
				<br/>
				<br/>
				Corcordo que, em caso de acidente, ou doença, por falta de imunização ou não, autorizo o responsável do Clube Pioneiros a tomar toda e qualquer decisão necessária para o restabelecimento da saúde do meu dependente,
				junto a todo e qualquer órgão que se fizer necessário, inclusive se houver necessidade de intervenção clinica ou cirúrgica.
				<br/>
				<br/>
				<br/>
				De acordo,
				</p>";
		$pdf->SetMargins(15, 0, 20, 0);
		$pdf->writeHTML($html, true, true, true, true);

		$pdf->SetXY(38,200);
		$pdf->Cell(0, 0, "São Paulo, ". utf8_encode(strftime("%e de %B de %Y",strtotime(date("Y-m-d")))), 0, false, 'L', false, false, 1, false, 'L', 'C');

		$pdf->Line(38, 230, 168, 230, $pdf->stLine3);
		$pdf->SetY(233);
		$pdf->SetFont(PDF_FONT_NAME_MAIN, 'I', 7);
		$pdf->Cell(0, 0, $linhaASS, 0, false, 'C', false, false, 1, false, 'L', 'C');

		$pdf->SetY(240);
		$pdf->SetTextColor(255,0,0);
		$pdf->SetFont(PDF_FONT_NAME_MAIN, 'B', 6);
		$pdf->Cell(0, 0, "OBRIGATÓRIO O RECONHECIMENTO DE FIRMA EM CARTÓRIO", 0, false, 'C', false, false, 1, false, 'L', 'C');
    endforeach;

    $pdf->download();
endif;
exit;
?>