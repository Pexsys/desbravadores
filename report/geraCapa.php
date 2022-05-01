<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class ESPCR extends TCPDF {
	
	//lines styles
	private $stLine;
	private $stLine2;
	private $params;
	private $top;
	
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
		
		$this->SetTitle('Geração automática de capas de especialidades');
		$this->SetSubject(PATTERNS::getClubeDS(array("cl","nm")));
		$this->SetKeywords('Especialidades, ' . str_replace(" ", ", ", PATTERNS::getClubeDS( array("db","nm","ibd") ) ));
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}

 	public function Header() {
	}
	
	public function Footer() {
		if (!empty($this->params[0])):
			$this->SetY(-20);
			$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 8);
			$this->Cell(0, 10, PATTERNS::getCDS(), 0, false, 'C');
		endif;
	}
	
	private function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
		$this->top = 10;
	}
	
	private function addRequisitoMestrado( $req, $f ){
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		$this->setXY(10, $this->top);
		$this->Cell(150, 5, "$req) Ter pelo menos " .$f["min"]. " das seguintes: ", '', 1, 'L', 1);
		$this->top += 5;
	}
	
	public function addEspecialidade($codEsp,$params) {
		$this->params = $params;
		$cadMembroID = $this->params[0];
		$nmPessoa = $this->params[1];
		$pessoaID = null;
		$membroID = null;
		
		if (!empty($cadMembroID)):
		    $result = CONN::get()->execute("
    			SELECT *
    			  FROM CON_ATIVOS
    			 WHERE ID_CAD_MEMBRO = ?
    	    ", array( $cadMembroID ) );
    	    if (!$result->EOF):
				$nmPessoa = $result->fields["NM"];
				$membroID = $result->fields["ID_MEMBRO"];
				$pessoaID = $result->fields["ID_CAD_PESSOA"];
            endif;
		endif;
		
		$result = CONN::get()->execute("
			SELECT ta.ID, ta.DS_ITEM, ta.CD_AREA_INTERNO, tm.NR_PG_ASS
			  FROM TAB_APRENDIZADO ta
		INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = ta.ID)
			 WHERE ta.CD_ITEM_INTERNO = ?
			   AND ta.TP_ITEM = 'ES'
		", array($codEsp));
 
		if ($result->EOF):
			return;
		endif;

		//achar o nome e a area com select
		$nomeEsp = $result->fields['DS_ITEM'];
		$areaEsp = $result->fields['CD_AREA_INTERNO'];
		$pgAss = $result->fields['NR_PG_ASS'];

		$this->AddPage();
		$this->setXY(0,0);
		$this->Image("img/aprendizado/ES/$areaEsp/$codEsp.jpg", 32, 30, 150, 114, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->Ln(130);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'BI', 25);
		$this->Cell(0, 0, $nomeEsp, 0, false, 'C', false, false, 1, false, 'C', 'C');

		$this->Ln(5);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 13);
		$this->writeHTMLCell(0, 0, '', '', "<span>$codEsp - #$pgAss</span>", 0, 0, 0, true, 'C', true); //<span style=\"color:#888888\">&nbsp;[".$result->fields["ID"]."]</span>
		
		if (!empty($membroID)):
			$barCODE = PATTERNS::getBars()->encode(array(
				"id" => "E",
				"fi" => $result->fields["ID"],
				"ni" => $membroID
			));
			$this->write1DBarcode($barCODE, 'C39', 73, 178, '', 17, 0.4, $this->stLine3, 'N');
		endif;
		
		$tbTop = 205;
		$this->setY($tbTop);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 17);
		$this->Cell(0, 0, $nmPessoa, 0, false, 'C', false, false, 1, false, 'C', 'C');
		
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
		
		//MONTA ESPECIALIDADES COMPLETADAS NA SEGUNDA FOLHA DESSE MESTRADO.
		if (!empty($pessoaID) && $areaEsp == "ME"):
		    
		    $fazReq = true;
		    $arr = array();
		    
            //LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
			$rR = CONN::get()->execute("
					SELECT tar.ID, tar.QT_MIN, COUNT(*) AS QT_FEITAS
						FROM TAB_APR_ITEM tar
				INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_ITEM = tar.ID AND car.TP_ITEM_RQ = 'ES')
				INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_INICIO IS NOT NULL)
						WHERE tar.ID_TAB_APREND = ?
					GROUP BY tar.ID, tar.QT_MIN
			", array( $pessoaID, $result->fields["ID"] ) );
			foreach($rR as $lR => $fR):
				$fazReq = ( $fR["QT_FEITAS"] >= $fR["QT_MIN"] );
				
				if (!$fazReq):
					break;
				endif;
					
				$arr[ $fR["ID"] ] = array(
					"min" => $fR["QT_MIN"],
					"hist" => array()
				);
					
				//ADICIONAR REGRA E SELECAO DA REGRA.
				//LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
				$rS = CONN::get()->execute("
					SELECT car.ID_RQ, car.CD_AREA_INTERNO_RQ, car.CD_ITEM_INTERNO_RQ, car.DS_ITEM_RQ, 
							tm.NR_PG_ASS, 
							ah.DT_INICIO, ah.DT_CONCLUSAO
						FROM CON_APR_REQ car
				INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
				INNER JOIN TAB_MATERIAIS tm ON (tm.ID_TAB_APREND = car.ID_RQ)
						WHERE car.ID_TAB_APR_ITEM = ?
					ORDER BY tm.NR_PG_ASS, car.CD_AREA_INTERNO_RQ, car.CD_ITEM_INTERNO_RQ 
				", array( $pessoaID, $fR["ID"] ) );
				foreach($rS as $lS => $fS):
					$arr[ $fR["ID"] ]["hist"][] = $fS;
                endforeach;
            endforeach;
    		
			//VERIFICA SE CONCLUIDO
			if ( $fazReq ):
                $this->newPage();
                
                $req = 0;
                foreach ($arr as $k => $i):
                    ++$req;
                    
                    //ADICIONA CABECALHO DO REQUISITO.
                    $this->startTransaction();
                	$start_page = $this->getPage();
                	$this->addRequisitoMestrado( $req, $i );
                	if  ($this->getNumPages() != $start_page):
                		$this->rollbackTransaction(true);
                		$this->newPage();
                		$this->addRequisitoMestrado( $req, $i );
                	else:
                		$this->commitTransaction();     
                	endif;
                	$this->top += 3;
                	
                	//ADICIONA ITENS DO REQUISITO
                    foreach ($i["hist"] as $j => $z):
                        $this->startTransaction();
                    	$start_page = $this->getPage();
                    	$this->addItemMestrado( $z );
                    	if  ($this->getNumPages() != $start_page):
                    		$this->rollbackTransaction(true);
                    		$this->newPage();
                    		$this->addItemMestrado( $z );
                    	else:
                    		$this->commitTransaction();     
                    	endif;
                    endforeach;
                    
                    $this->top += 20;
                endforeach;
			endif;
			
			//REMOVE NOTIFICACAO PARA O MESTRADO
			CONN::get()->execute("
				UPDATE LOG_MENSAGEM SET DH_READ = NOW()
				WHERE ID_ORIGEM = ? 
				AND TP = ? 
				AND ID_CAD_USUARIO = (SELECT ID FROM CAD_USUARIO WHERE ID_CAD_PESSOA = ?)
			", array( $result->fields["ID"], "M", $pessoaID ) );

		endif;
	}

	private function addItemMestrado( $f ){
		$areaEsp = $f['CD_AREA_INTERNO_RQ'];
	    $codEsp = $f['CD_ITEM_INTERNO_RQ'];
	    
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		$this->setXY(15, $this->top);
		$this->Image("img/aprendizado/ES/$areaEsp/$codEsp.jpg", 13, $this->top, 26, 21, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		$this->top += 2;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 10);
		$this->SetFillColor(100,100,100);
		$this->SetTextColor(255,255,255);
		$this->RoundedRect(40, $this->top, 160, 6, 2.5, '1001', 'FD', $this->stLine2);
		$this->setXY(44, $this->top+1);
		$this->Cell(140, 4, $f['DS_ITEM_RQ'], '', 1, 'L', 1, '', 0, false, 'T', 'C');
		$this->top += 6;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 6);
		$this->SetFillColor(180,180,180);
		$this->setXY(40, $this->top);
		$this->Cell(16, 4, "Código", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(56, $this->top);
		$this->Cell(16, 4, "Assinatura", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(72, $this->top);
		$this->Cell(32, 4, "Início", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(104, $this->top);
		$this->Cell(32, 4, "Conclusão", 'TL', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(136, $this->top);
		$this->Cell(64, 4, "Instrutor", 'TLR', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->top += 4;
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		$this->setXY(40, $this->top);
		$this->Cell(16, 7, $codEsp, '', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(56, $this->top);
		$this->Cell(16, 7, "#".$f['NR_PG_ASS'], 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(72, $this->top);
		$this->Cell(32, 7, (is_null($f["DT_INICIO"]) ? "____/____/_________" : strftime("%d/%m/%Y",strtotime($f["DT_INICIO"]))), 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(104, $this->top);
		$this->Cell(32, 7, (is_null($f["DT_CONCLUSAO"]) ? "____/____/_________" : strftime("%d/%m/%Y",strtotime($f["DT_CONCLUSAO"]))), 'L', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->setXY(136, $this->top);
		$this->Cell(64, 7, "", '', 1, 'C', 1, '', 0, false, 'T', 'C');
		$this->RoundedRect(40, $this->top, 96, 7, 2.5, '0010', 'D', $this->stLine2);
		$this->RoundedRect(136, $this->top, 64, 7, 2.5, '0100', 'D', $this->stLine2);
        $this->top += 10;
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



$pdf = new ESPCR();

$arrNome = array();
if ($nome == "ALL"):
	$result = CONN::get()->execute("
		SELECT *
		  FROM CON_ATIVOS 
		 ORDER BY NM");
	foreach ($result as $k => $line):
		$arrNome[] = $line["ID_CAD_MEMBRO"];
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
