<?php
@require_once('../include/functions.php');
@require_once('../include/_core/lib/tcpdf/tcpdf.php');

class ESPCR extends TCPDF {

	//lines styles
	private $stLine;
	private $stLine2;
	private $stLine3;
	private $line;
	private $dsCargo;
	public $campori;
	public $SEQ;

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

		$this->SetTitle('Geração automática de autorização de Saída');
		$this->SetSubject(PATTERNS::getClubeDS(array("cl","nm")));
		$this->SetKeywords('Autorizações, ' . str_replace(" ", ", ", PATTERNS::getClubeDS( array("db","nm","ibd") ) ) );
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->SetTopMargin(4);
		$this->SetFooterMargin(0);
		$this->SetLeftMargin(4);
		$this->SetRightMargin(4);
		$this->SetHeaderMargin(0);
		$this->SetFooterMargin(0);

		$this->SEQ = array();
		$this->campori = false;
	}

	public function setLine($line){
	    $this->line = $line;
	    $this->campori = ($this->line["TP_AUTORIZ"] == "OP-CAM");
	    $arr = explode(' ',mb_strtolower($this->line["DS_CARGO"], "UTF-8"));
	    $this->dsCargo = (fStrStartWith($this->line["CD_CARGO"],"1") ? ($this->line["TP_SEXO"] == "F" ? "desbravadora" : "desbravador") : $arr[0]);
	}

 	public function Header() {
 	    if ($this->campori):
      		$this->setXY(0,0);
     		$this->Image("img/iasd-full.jpg", 5, 7, 30, 28, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
     		$this->Image("img/logod1.jpg", 35, 9, 23, 23, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

     		$this->Line(170, 7, 170, 35, $this->stLine2);

     		$this->setXY(172,7);
     		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 7);
     		$this->Cell(44, 4, "Distrito de ".PATTERNS::getClubeDS( array("dst") ), 0, false, 'L', false, false, false, false, 'T', 'M');
     		$this->setXY(172,11);
     		$this->Cell(44, 4, PATTERNS::getClubeDS( array("add") ), 0, false, 'L', false, false, false, false, 'T', 'M');
     		$this->setXY(172,15);
     		$this->Cell(44, 4, PATTERNS::getClubeDS( array("dst") ), 0, false, 'L', false, false, false, false, 'T', 'M');
     		$this->setXY(172,19);
     		$this->Cell(44, 4, PATTERNS::getClubeDS( array("cid") ), 0, false, 'L', false, false, false, false, 'T', 'M');
     		$this->setXY(172,23);
     		$this->Cell(44, 4, PATTERNS::getClubeDS( array("cep") ), 0, false, 'L', false, false, false, false, 'T', 'M');
     		$this->setXY(172,27);
     		$this->Cell(44, 4, PATTERNS::getClubeDS( array("cnpj") ), 0, false, 'L', false, false, false, false, 'T', 'M');
     		$this->setXY(172,31);
     		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 8);
     		$this->SetTextColor(0,128,128);
     		$this->Cell(44, 4, PATTERNS::getClubeDS( array("as") ), 0, false, 'L', false, false, false, false, 'T', 'M');

     		$this->setXY(5,35);
     		$this->SetTextColor(0,0,0);
     		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 13);
     		$this->Cell(200, 8, $this->line["DS"], 0, false, 'C', false, false, false, false, 'T', 'M');

     		$this->setXY(5,43);
     		$this->SetTextColor(255,0,0);
     		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 16);
     		$this->Cell(200, 8, "\"".$this->line["DS_TEMA"]."\"", 0, false, 'C', false, false, false, false, 'T', 'M');

     		$this->setXY(5,65);
     		$this->SetTextColor(0,0,0);
     		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 14);
     		$this->Cell(200, 8, "Autorização de Saída e Participação", 0, false, 'C', false, false, false, false, 'T', 'M');
     	endif;
	}

	public function Footer() {
 	    if ($this->campori):
    		$this->SetTextColor(0,0,0);
    		$this->SetFont(PDF_FONT_NAME_MAIN, 'N', 5);
    		$this->SetY(-9);
    		$this->Cell(205, 5, "Esta autorização perderá automaticamente o valor em caso de rasuras, dobras, ratificações, ressalvas ou adendos ao texto sem o prévio acordo com o DIRETOR.", 0, false, 'C');

    		$this->Image("img/logo.jpg", 183, 263, 20, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    	endif;
	}

	public function newPage() {
		$this->AddPage();
		$this->setCellPaddings(0,0,0,0);
		$this->SetTextColor(0,0,0);
		$this->setXY(0,0);
	}

	public function addAutorizacao( $aP ) {
		$lBase = (($aP-1)*70)+4;

		$dtS = strtotime($this->line["DH_S"]);
		$dtR = strtotime($this->line["DH_R"]);
		$linhaASS = trim($this->line["NM_RESP"]).", ".$this->line["NR_DOC_RESP"].", CPF ".fCPF($this->line["NR_CPF_RESP"]). (!empty($this->line["FONE_CEL_RESP"]) ? ", Fone ".$this->line["FONE_CEL_RESP"] : "");
		$barCODE = PATTERNS::getBars()->encode(array(
			"id" => "D",
			"fi" => $this->line["ID"],
			"ni" => $this->line["ID_MEMBRO"]
		));

		$lBase+=4;
		$this->SetY($lBase);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 14);
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
		$this->SetTextColor(255,0,0);
		$this->Cell(20, 0, strftime("%Y",$dtS)."-".fStrZero($this->line["ID"],3), 0, false, 'L', false, false, 1, false, 'C', 'C');

		$this->SetX(83);
		$this->SetTextColor(0,0,0);
		$this->Cell(0, 0, "Autorização de Saída", 0, 0, 'L', false, false, 0, false, 'C', 'C');

		$this->SetX(195);
		$this->SetTextColor(180,180,180);
		$this->Cell(10, 0, fStrZero($this->SEQ[$this->line["ID"]."|".$this->line["ID_CAD_MEMBRO"]],zeroSizeID()), 0, false, 'R', false, false, 1, false, 'C', 'C');

		$this->SetY($lBase+6);
		$this->SetTextColor(0,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, '', 9);

		$html = "<p align=\"justify\">Através dos poderes legais a mim atribuídos, autorizo ".
			($this->line["TP_SEXO"] == "F" ? "a " : "o "). $this->dsCargo ." <b><u>".trim($this->line["NM"])."</u></b>, ".
			$this->line["NR_DOC"]." a participar juntamente com o ".PATTERNS::getClubeDS(array("cl","nm")).", dirigido e representado por ".trim($this->line["NOME_DIRETOR"]).", ".
			$this->line["IDENT_DIRETOR"].", do evento: ".trim($this->line["DS"]). (!empty($this->line["DS_DEST"]) ? "/".trim($this->line["DS_DEST"]) : "").", ".
			(strftime("%Y-%m-%d",$dtS) == strftime("%Y-%m-%d",$dtR)
				? "no dia ". utf8_encode(strftime("%e de %B de %Y",$dtS)). ", saindo &agrave;s ". strftime("%Hh". (strftime("%M",$dtS)>0?"%M":""),$dtS). " e retornando &agrave;s ". strftime("%Hh". (strftime("%M",$dtR)>0?"%M":""),$dtR)
				: ", com saída prevista para ". utf8_encode(strftime("%e de %B de %Y &agrave;s %Hh". (strftime("%M",$dtS)>0?"%M":""),$dtS))." e com retorno previsto para ". utf8_encode(strftime("%e de %B de %Y &agrave;s %Hh". (strftime("%M",$dtR)>0?"%M":""),$dtR))
			).
			". Local de Saída/Retorno: ".trim($this->line["DS_ORIG"]). ". ".
			"Consciente dos grandes benefícios recebidos através do ".PATTERNS::getClubeDS(array("cl","cj","db")).
			" acima descrito, abdico responsabilizar, em qualquer instância judicial, o(os) responsável(eis) do referido Clube em todos os níveis, bem como a ".
			"Igreja Adventista do Sétimo Dia, por qualquer dano causado ou sofrido por meu dependente, devido a sua própria atuação, ".
			"no percurso de ida e volta bem como no decurso do referido evento. Em caso de acidente, ou doença, autorizo o responsável do ".
			"Referido Clube a tomar toda e qualquer decisão necessária para o restabelecimento da saúde do meu dependente, junto a todo e ".
			"qualquer órgão que se fizer necessário, inclusive se houver necessidade de intervenção clinica ou cirúrgica.</p>";
		$this->writeHTML($html, true, false, true, true);

		$this->setXY(117,$lBase+40);
		$this->write1DBarcode($barCODE, 'C39', '', '', '', 18, 0.4, $this->stLine, 'N');
		$this->Image("img/logo.jpg", 187, $lBase+40, 17, 18, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->Line(4, $lBase+53, 115, $lBase+53, $this->stLine3);
		$this->SetY($lBase+56);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 6);
		$this->Cell(0, 0, $linhaASS, 0, false, 'L', false, false, 1, false, 'L', 'C');

		$this->Line(0, $lBase+65, 220, $lBase+65, $this->stLine2);
	}

	public function addAutorizacaoCampori() {
	    $this->newPage();

		$dtS = strtotime($this->line["DH_S"]);
		$dtR = strtotime($this->line["DH_R"]);
		$linhaASS = trim($this->line["NM_RESP"]).", ".$this->line["NR_DOC_RESP"].", CPF ".fCPF($this->line["NR_CPF_RESP"]). (!empty($this->line["FONE_CEL_RESP"]) ? ", Fone ".$this->line["FONE_CEL_RESP"] : "");

		$this->SetTextColor(0,0,0);
		$this->SetY(85);
		$this->SetFont(PDF_FONT_NAME_MAIN, '', 10);
		$html = "<p align=\"justify\">Eu, ". trim($this->line["NM_RESP"]) .", autorizo ".
				($this->line["TP_SEXO"] == "F" ? "a " : "o "). $this->dsCargo ." <b><u>".trim($this->line["NM"])."</u></b>, ".
				$this->line["NR_DOC"]." a se deslocar e participar juntamente com o ".PATTERNS::getClubeDS(array("cl","cj","db","nm"))." do ". trim($this->line["DS"]) .", \"". trim($this->line["DS_TEMA"]) ."\"".
				", promovido pela ".trim($this->line["DS_ORG"]) .
				" da Igreja Adventista do Sétimo Dia, que se realizará entre ". utf8_encode(strftime("%e de %B de %Y &agrave;s %Hh". (strftime("%M",$dtS)>0?"%M":""),$dtS)) .
				" e com retorno em ". utf8_encode(strftime("%e de %B de %Y &agrave;s %Hh". (strftime("%M",$dtR)>0?"%M":""),$dtR)) .
				", no ".trim($this->line["DS_DEST"]) .".
				<br/>
				<br/>
				Através dos poderes legais a mim atribuídos, nomeio através desta para o período do evento, como responsável pelo menor acima descrito".
				" o DIRETOR e responsável pelo ".PATTERNS::getClubeDS(array("cl","cj","db","nm")).", identificado nesta como ".trim($this->line["NOME_DIRETOR"]).", ".$this->line["IDENT_DIRETOR"].".
				<br/>
				<br/>
				Consciente dos grandes benefícios recebidos através do ".PATTERNS::getClubeDS(array("cl","cj","db"))." acima descrito, abdico responsabilizar,
				em qualquer instância judicial, o(os) responsável(eis) do referido Clube em todos os níveis, bem como a
				Igreja Adventista do Sétimo Dia, por qualquer dano causado ou sofrido por meu dependente, devido a sua própria atuação,
				no percurso de ida e volta bem como no decurso do referido evento.
				<br/>
				<br/>
				Em caso de acidente, ou doença, autorizo o responsável do
				referido Clube a tomar toda e qualquer decisão necessária para o restabelecimento da saúde do meu dependente, junto a todo e
				qualquer órgão que se fizer necessário, inclusive se houver necessidade de intervenção clinica ou cirúrgica.
				<br/>
				<br/>
				<br/>
				De acordo,
				</p>";
		$this->SetMargins(15, 0, 20, 0);
		$this->writeHTML($html, true, true, true, true);

		$this->SetXY(38,200);
		$this->Cell(0, 0, "São Paulo, ". utf8_encode(strftime("%e de %B de %Y",strtotime(date("Y-m-d")))), 0, false, 'L', false, false, 1, false, 'L', 'C');

		$this->Line(38, 230, 168, 230, $this->stLine3);
		$this->SetY(233);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 7);
		$this->Cell(0, 0, $linhaASS, 0, false, 'C', false, false, 1, false, 'L', 'C');

		$this->SetY(240);
		$this->SetTextColor(255,0,0);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 6);
		$this->Cell(0, 0, "OBRIGATÓRIO O RECONHECIMENTO DE FIRMA EM CARTÓRIO", 0, false, 'C', false, false, 1, false, 'L', 'C');

		$barCODE = PATTERNS::getBars()->encode(array(
			"id" => "F",
			"fi" => $this->line["ID"],
			"ni" => $this->line["ID_MEMBRO"]
		));

		$this->setXY(62,255);
		$this->SetTextColor(0,0,0);
		$this->write1DBarcode($barCODE, 'C39', '', '', '', 20, 0.5, $this->stLine, 'N');

		$this->SetXY(5,265);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'B', 11);
		$this->SetTextColor(255,0,0);
		$this->Cell(20, 3, strftime("%Y",$dtS)."-".fStrZero($this->line["ID"],3), 0, false, 'L', false, false, 1, false, 'C', 'C');

		$this->SetXY(5,270);
		$this->SetTextColor(180,180,180);
		$this->Cell(10, 3, fStrZero($this->SEQ[$this->line["ID"]."|".$this->line["ID_CAD_MEMBRO"]],zeroSizeID()), 0, false, 'L', false, false, 1, false, 'C', 'C');
	}

	public function download() {
		$this->lastPage();
		$this->Output("AutorizacaoDeSaida_".date('Y-m-d_H:i:s').".pdf", 'I');
	}
}

$list = false;
$pID = fRequest("pid");
$eventoID = fRequest("list");
if ( isset($eventoID) && strlen($eventoID) > 0 ):
	$pID = fRequest("pid");
	if ( $pID == "null" ):
		echo "NOME INVÁLIDO!";
		exit;
	endif;
	$list = true;
else:
	$eventoID = fRequest("eve");
endif;

if ( ( !isset($eventoID) || empty($eventoID) || stristr($eventoID, "indispon") ) && ( !isset($pID) || empty($pID) ) ):
	echo "AUTORIZA&Ccedil;&Atilde;O N&Atilde;O ENCONTRADA!";
	exit;
endif;



//SE EVENTO NULO, E PESSOA NAO NULA, SETA TODOS OS EVENTOS DA PESSOA.
if ( (!isset($eventoID) || empty($eventoID)) && (isset($pID) || !empty($pID)) ):
   $result = CONN::get()->execute("
        SELECT es.ID
    	  FROM EVE_SAIDA es
    INNER JOIN EVE_SAIDA_MEMBRO esp ON (esp.ID_EVE_SAIDA = es.ID AND esp.FG_AUTORIZ = 'S')
         WHERE es.DH_R > NOW()
    	   AND esp.ID_CAD_MEMBRO IN ($pID)
      ORDER BY 1
    ");
    $eventoID = "";
    foreach ($result as $k => $f):
        $eventoID .= ",".$f["ID"];
    endforeach;
    $eventoID = substr($eventoID,1);
    $list = true;
endif;

if ( ( !isset($eventoID) || empty($eventoID) || stristr($eventoID, "indispon") ) ):
	echo "AUTORIZA&Ccedil;&Atilde;O N&Atilde;O ENCONTRADA!";
	exit;
endif;

$pdf = new ESPCR();

//DEFINE SEQUENCIA POR ID PESSOA
$query = "
    	SELECT esp.ID_EVE_SAIDA, esp.ID_CAD_MEMBRO, ca.NM
    	  FROM EVE_SAIDA_MEMBRO esp
    INNER JOIN CON_ATIVOS ca ON (ca.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO)
         WHERE esp.FG_AUTORIZ = 'S'
		   ".($list ? " AND esp.ID_EVE_SAIDA IN ($eventoID) " : "AND esp.ID_EVE_SAIDA = $eventoID")."

		   AND esp.ID_CAD_MEMBRO NOT IN (
				SELECT m.ID
				FROM CAD_MEMBRO m
				INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
				INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = p.ID_CAD_PESSOA)
				WHERE
					(
						LENGTH(p.NM)<=5
						OR p.TP_SEXO NOT IN ('M','F')
						OR (p.DT_NASC IS NULL OR LENGTH(p.DT_NASC) = 0)
						OR (p.NR_DOC IS NULL OR LENGTH(p.NR_DOC) < 7 OR INSTR(TRIM(p.NR_DOC),' ') < 3)
						OR ((p.NR_CPF IS NULL OR LENGTH(p.NR_CPF)=0) AND p.NR_CPF_RESP IS NULL)
						OR (p.LOGRADOURO IS NULL OR LENGTH(p.LOGRADOURO) = 0)
						OR (p.NR_LOGR IS NULL OR LENGTH(p.NR_LOGR) = 0)
						OR (p.BAIRRO IS NULL OR LENGTH(p.BAIRRO) = 0)
						OR (p.CIDADE IS NULL OR LENGTH(p.CIDADE) = 0)
						OR (p.UF IS NULL OR LENGTH(p.UF) = 0)
						OR (p.CEP IS NULL OR LENGTH(p.CEP) = 0)
						OR ((p.FONE_RES IS NULL AND p.FONE_CEL IS NULL) OR LENGTH(CONCAT(p.FONE_RES,p.FONE_CEL)) = 0)
						OR (p.IDADE_ANO < 18 AND (p.ID_PESSOA_RESP IS NULL OR (p.NR_DOC_RESP IS NULL OR LENGTH(p.NR_DOC_RESP) < 7 OR INSTR(TRIM(p.NR_DOC_RESP),' ') < 3) OR (p.NR_CPF_RESP IS NULL OR LENGTH(p.NR_CPF_RESP)=0)))
						OR (a.ID_UNIDADE IS NULL OR a.ID_UNIDADE = 0)
						OR (a.CD_CARGO IS NULL OR LENGTH(a.CD_CARGO)<=1)
					)
				)

    	 ORDER BY esp.ID_EVE_SAIDA, ca.NM
";
$result = CONN::get()->execute($query);
$i = 0;
$ant = $result->fields["ID_EVE_SAIDA"];
foreach ($result as $k => $line):
    if ($ant !== $line["ID_EVE_SAIDA"]):
        $ant = $line["ID_EVE_SAIDA"];
        $i = 0;
    endif;
    $pdf->SEQ[ $line["ID_EVE_SAIDA"]."|".$line["ID_CAD_MEMBRO"] ] = ++$i;
endforeach;
//print_r($pdf->SEQ);

$aP = 0;
$query = "
	SELECT es.ID, es.DS, es.DH_S, es.DH_R, es.DS_TEMA, es.DS_ORG, es.DS_DEST, es.DS_ORIG, es.TP_AUTORIZ,
	       ca.ID_MEMBRO, ca.ID_CAD_MEMBRO, ca.NM, ca.TP_SEXO, ca.NR_DOC, ca.NR_CPF, ca.TP_SEXO_RESP, ca.DS_TP,
		   ca.NM_RESP, ca.NR_DOC_RESP, ca.NR_CPF_RESP, ca.FONE_CEL_RESP, ca.CD_CARGO, ca.DS_CARGO,
	       cd.NOME_DIRETOR, cd.IDENT_DIRETOR
	  FROM EVE_SAIDA es,
	       EVE_SAIDA_MEMBRO esp,
	       CON_ATIVOS ca,
	       CON_DIRETOR cd
         WHERE esp.ID_EVE_SAIDA = es.ID
           AND esp.FG_AUTORIZ = 'S'
           AND ca.ID_CAD_MEMBRO = esp.ID_CAD_MEMBRO
	   ".($list ? " AND esp.ID_CAD_MEMBRO IN ($pID) AND esp.ID_EVE_SAIDA IN ($eventoID) " : "AND es.ID = $eventoID")."

	   AND esp.ID_CAD_MEMBRO NOT IN (
		SELECT m.ID
		FROM CAD_MEMBRO m
		INNER JOIN CON_PESSOA p ON (p.ID_CAD_PESSOA = m.ID_CAD_PESSOA)
		INNER JOIN CON_ATIVOS a ON (a.ID_CAD_PESSOA = p.ID_CAD_PESSOA)
		WHERE
			(
				LENGTH(p.NM)<=5
				OR p.TP_SEXO NOT IN ('M','F')
				OR (p.DT_NASC IS NULL OR LENGTH(p.DT_NASC) = 0)
				OR (p.NR_DOC IS NULL OR LENGTH(p.NR_DOC) < 7 OR INSTR(TRIM(p.NR_DOC),' ') < 3)
				OR ((p.NR_CPF IS NULL OR LENGTH(p.NR_CPF)=0) AND p.NR_CPF_RESP IS NULL)
				OR (p.LOGRADOURO IS NULL OR LENGTH(p.LOGRADOURO) = 0)
				OR (p.NR_LOGR IS NULL OR LENGTH(p.NR_LOGR) = 0)
				OR (p.BAIRRO IS NULL OR LENGTH(p.BAIRRO) = 0)
				OR (p.CIDADE IS NULL OR LENGTH(p.CIDADE) = 0)
				OR (p.UF IS NULL OR LENGTH(p.UF) = 0)
				OR (p.CEP IS NULL OR LENGTH(p.CEP) = 0)
				OR ((p.FONE_RES IS NULL AND p.FONE_CEL IS NULL) OR LENGTH(CONCAT(p.FONE_RES,p.FONE_CEL)) = 0)
				OR (p.IDADE_ANO < 18 AND (p.ID_PESSOA_RESP IS NULL OR (p.NR_DOC_RESP IS NULL OR LENGTH(p.NR_DOC_RESP) < 7 OR INSTR(TRIM(p.NR_DOC_RESP),' ') < 3) OR (p.NR_CPF_RESP IS NULL OR LENGTH(p.NR_CPF_RESP)=0)))
				OR (a.ID_UNIDADE IS NULL OR a.ID_UNIDADE = 0)
				OR (a.CD_CARGO IS NULL OR LENGTH(a.CD_CARGO)<=1)
			)
		)
		
	 ORDER BY ca.NM
";
$result = CONN::get()->execute($query);
if (!$result->EOF):
	foreach ($result as $k => $line):
	    $pdf->setLine( $line );

	    if ($pdf->campori):
	        $pdf->addAutorizacaoCampori();
	    else:
    		if (++$aP == 1):
    			$pdf->newPage();
    		endif;
    	    $pdf->addAutorizacao($aP);
    		if ($aP == 4):
    			$aP = 0;
    		endif;
    	endif;
	endforeach;
	$pdf->download();
endif;
exit;
?>
