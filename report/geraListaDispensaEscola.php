<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class LISTADISPENSAESCOLAR extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	public $lineAlt;
	public $posY;
	
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
		$this->SetTitle('Listagem de Dispensa Escolar');
		$this->SetSubject($GLOBALS['pattern']->getClubeDS(array("cl","nm")));
		$this->SetKeywords('Dispensa, ' . str_replace(" ", ", ", $GLOBALS['pattern']->getClubeDS( array("db","nm","ig") ) ));
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
 		$this->Cell(44, 5, "Distrito de Capão Redondo", 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,12);
 		$this->Cell(44, 5, "Av. Ellis Maas, 520", 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,17);
 		$this->Cell(44, 5, "Capão Redondo", 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,22);
 		$this->Cell(44, 5, "São Paulo - SP", 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,27);
 		$this->Cell(44, 5, "CEP 05859-000", 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,32);
 		$this->Cell(44, 5, "CNPJ 43.586.122/0121-20", 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->setXY(163,37);
 		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
 		$this->SetTextColor(0,128,128);
 		$this->Cell(44, 5, "Associação Paulista Sul", 0, false, 'L', false, false, false, false, 'T', 'M');
 		$this->SetTextColor(0,0,0);
 		$this->posY = 48;
 	}
 	
 	public function addLine($f){
 	    if ($this->lineAlt):
			$this->SetFillColor(245,245,245);
		else:
			$this->SetFillColor(255,255,255);
		endif;
		$this->lineAlt = !$this->lineAlt;
        $this->setXY(20, $this->posY);
        $this->Cell(175, 5, $f["NM"], 0, false, 'L', true);
        $this->posY += 5; 	    
 	}
 	
 	public function finishSchool($nmDiretor){
        $this->startTransaction();
		$start_page = $this->getPage();
		$this->ends($nmDiretor);
		if  ($this->getNumPages() != $start_page):
			$this->rollbackTransaction(true);
			$this->newPage();
			$this->ends($nmDiretor);
		else:
			$this->commitTransaction();     
		endif;	    
 	}
 	
 	public function ends($nmDiretor){
 	    $this->setCellPaddings(0,0,0,0);
    	$this->posY += 3;
    	$this->SetFillColor(255,255,255);
    	$this->setXY(10, $this->posY);
    	
        $html = "
            <p align=\"justify\">Certos de poder contar com seu apoio ao nosso trabalho subscrevemo-nos.<br/>
                Atenciosamente,
            </p>
        ";
        $this->setCellHeightRatio(2);
        $this->writeHTMLCell(0,0,10,$this->posY,$html,0,0,false,true,"",false);
        $this->posY += $this->getLastH()+10;
    	$this->setXY(10, $this->posY);
    	$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
    	$this->Cell(120, 4, $nmDiretor, 0, false, 'L', true);
    	$this->posY += 4;
    	$this->setXY(10, $this->posY);
    	$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
    	$this->SetTextColor(120,120,120);
    	$this->Cell(30, 3, "Diretor do Clube", 0, false, 'L', true);
 	}

	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function download() {
		$this->lastPage();
		$this->Output("ListagemDispensaEscolar_".date('Y-m-d_H:i:s').".pdf", "I");
	}
}

$eveID = fRequest("eve");
fConnDB();

$result = $GLOBALS['conn']->Execute("SELECT NOME_DIRETOR, NOW() AS DH FROM CON_DIRETOR");
$nmDiretor = titleCase($result->fields["NOME_DIRETOR"]);
$dh = $result->fields["DH"];

$escolaAnt = "";

$result = $GLOBALS['conn']->Execute("
	SELECT ca.NM_ESCOLA, ca.NM, es.DH_S, es.DH_R
	FROM EVE_SAIDA es
	INNER JOIN EVE_SAIDA_MEMBRO esp on (esp.ID_EVE_SAIDA = es.ID AND esp.FG_AUTORIZ = 'S')
	INNER JOIN CAD_MEMBRO cm on (cm.ID = esp.ID_CAD_MEMBRO)
	INNER JOIN CAD_ATIVOS at on (at.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO AND at.NR_ANO = YEAR(es.DH_R))
	INNER JOIN TAB_CARGO cg ON (cg.CD = at.CD_CARGO)
	INNER JOIN CON_PESSOA ca on (ca.ID_CAD_PESSOA = cm.ID_CAD_PESSOA)
	 WHERE es.ID = ?
	   AND ca.NM_ESCOLA IS NOT NULL
  ORDER BY ca.NM_ESCOLA, ca.NM
", array($eveID) );
if (!$result->EOF):
    $pdf = new LISTADISPENSAESCOLAR();
    
    foreach ($result as $k => $f):
        
        if ( $escolaAnt != trim($f["NM_ESCOLA"]) ):
            if ($escolaAnt != ""):
                $pdf->finishSchool($nmDiretor);
            endif;
            $escolaAnt = trim($f["NM_ESCOLA"]);
            
            $pdf->newPage();
    
            $pdf->setXY(10,$pdf->posY);
            $pdf->SetFont(PDF_FONT_NAME_MAIN, 'N', 9);
            $pdf->Cell(100, 5, "São Paulo, ".strftime("%d de %B de %Y",strtotime($dh)), 0, false, 'L', false, false, false, false, 'T', 'M');
            $pdf->posY += 10;
        
            $pdf->setXY(10,$pdf->posY);
            $pdf->Cell(100, 5, "A(o)", 0, false, 'L', false, false, false, false, 'T', 'M');
            $pdf->posY += 5;
        
            $pdf->setXY(10,$pdf->posY);
            $pdf->SetFont(PDF_FONT_NAME_MAIN, 'B', 9);
            $pdf->Cell(100, 5, $escolaAnt, 0, false, 'L', false, false, false, false, 'T', 'M');
            $pdf->posY += 10;
            
            $dhs = strtotime($f["DH_S"]);
            $dhr = strtotime($f["DH_R"]);
            
            if ( strftime("%B",$dhs) != strftime("%B",$dhr) ):
                $datas = strftime("%d",$dhs). " de " .strftime("%B",$dhs). " a ". strftime("%d",$dhr) ." de ". strftime("%B",$dhr);
            else:
                $datas = strftime("%d",$dhs) ." a ". strftime("%d",$dhr) ." de ". strftime("%B",$dhr);
            endif;
        
            $pdf->SetFont(PDF_FONT_NAME_MAIN, 'N', 8);
            $html = "
                <p align=\"justify\">Prezados Senhores,<br/>
                    <br/>
                    Pertencemos ao ".$GLOBALS['pattern']->getClubeDS(array("cl","cj","db")).", órgão pertencente à Igreja Adventista do 7º Dia, que tem por finalidade auxiliar os pais na formação do caráter de seus filhos, na faixa etária de 10 a 15 anos. O trabalho que realizamos compreende atividades, tais como:
                    Projetos Comunitários: limpeza de praças, plantio de árvores, pintura de meio fio, desfiles anti-fumo e álcool, desfiles cívicos, visitas a asilos e orfanatos, auxílio em campanhas de vacinação, entre outros, que promovem a conscientização de preservação do meio onde vivemos, respeitando a vida e os semelhantes, além de orientá-los para um viver mais saudável;&nbsp;
                    Aulas teóricas e práticas de especializações nas mais diversas áreas: dando-os a oportunidade de identificarem-se com áreas para futura profissão;&nbsp;
                    Desenvolvimento de atividades físicas: condicionamento físico e modalidades esportivas;&nbsp;
                    Apoio aos pais na orientação de seus filhos adolescentes;&nbsp;
                    Acampamentos e caminhadas: para educação ecológica e ambiental;<br/>
                    São atividades exclusivamente preparadas para a faixa etária e desenvolvidas durante os domingos e muitas vezes em alguns períodos do ano. Nos próximos dias $datas estaremos participando de um evento denominado “Campori”, onde estarão concentrados os desbravadores, dos mais diversos clubes e locais. Os desbravadores aguardam ansiosamente por este evento todos os anos, e por ser este um programa especial e importante para nossos meninos e meninas, solicitamos seu apoio, dispensando os(as) alunos(as) abaixo citados, nos dias citados acima, colaborando para que possam fazer provas e entregar trabalhos em outra data, sendo que o desbravador estará ciente de que deverá recuperar toda a matéria perdida, e não ter excedido o seu número de faltas limite para o ano.
                </p>
            ";
            $pdf->setCellHeightRatio(2);
            $pdf->writeHTMLCell(0,0,10,$pdf->posY,$html,0,0,false,true,"",false);
            
        	$pdf->posY += $pdf->getLastH()+4;
        	$pdf->setCellHeightRatio(0);
        	$pdf->SetTextColor(255,255,255);
        	$pdf->SetFillColor(100,100,100);
        	$pdf->setCellPaddings(1,0,1,0);
        	$pdf->setXY(20, $pdf->posY);
        	$pdf->Cell(175, 5, "Nome Completo", 0, false, 'L', true);
        	$pdf->posY += 5;
        	$pdf->SetTextColor(0,0,0);
        	$pdf->lineAlt = false;
        endif;
        
        $pdf->startTransaction();
		$start_page = $pdf->getPage();
		$pdf->addLine($f);
		if  ($pdf->getNumPages() != $start_page):
			$pdf->rollbackTransaction(true);
			$pdf->newPage();
			$pdf->addLine($f);
		else:
			$pdf->commitTransaction();     
		endif;

    endforeach;
    $pdf->finishSchool($nmDiretor);
    $pdf->download();
endif;


/*

FABIANO MESSIAS DA SILVA




*/
exit;
?>