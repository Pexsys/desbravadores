<?php
class BARS {
	private $bars;
	
	function __construct() {
		$this->bars = array(
				//CLUBE ID
				"CI" => "P",
				
				//FUNCTION ID
				"ID" => array(
						"length" => 1,
						"types" => array(
								//ID ETIQUETA           //FUNCTION          //IMPR.ETIQ     //OBR.CLASSE    //MODELO FORM   //DESCRICAO
								array(  "id" => "0",    "fn" => "BS_NM",    "tg" => "S",    "cl" => "N",    "md" => "1",    "ds" => "0-BÁSICA/NOME" ),
								array(  "id" => "1",    "fn" => "AV_CL",    "tg" => "S",    "cl" => "S",    "md" => "3",    "ds" => "1-CAPA DA PASTA DE AVALIAÇÃO" ),
								array(  "id" => "2",	"fn" => "CL_BL",    "tg" => "S",    "cl" => "S",    "md" => "3",    "ds" => "2-CAPA DE LEITURA BÍBLICA" ),
								array(  "id" => "A",	"fn" => "CT_CL",    "tg" => "S",    "cl" => "S",    "md" => "1",    "ds" => "A-CARTÃO DE CLASSE" ),
								array(  "id" => "B",	"fn" => "CD_CL",    "tg" => "S",    "cl" => "S",    "md" => "1",    "ds" => "B-CADERNO DE ATIVIDADES" ),
								array(  "id" => "C",	"fn" => "PT_CL",    "tg" => "S",    "cl" => "S",    "md" => "2",    "ds" => "C-PASTA DE CLASSE" ),
								array(  "id" => "D",	"fn" => "AT_CM",    "tg" => "N",    "cl" => "N",                    "ds" => "D-AUTORIZAÇÃO DE SAÍDA" ),
								array(  "id" => "E",	"fn" => "CT_ES",    "tg" => "S",    "cl" => "N",    "md" => "1",    "ds" => "E-CARTÃO / ESPECIALIDADES" ),
								array(  "id" => "F",	"fn" => "AT_ES",    "tg" => "N",    "cl" => "N",                    "ds" => "F-AUTORIZAÇÃO ESPECIAL" )
						)
				),
				
				//FUNCTION ID PARAM
				"FI" => 2,
				
				//PEOPLE ID - FROM PARAM
				"NI" => 3
		);
	}
	
	public function getLength(){
		return
		strlen($this->getClubeID()) +
		$this->getIDLength()+
		$this->getFILength()+
		$this->getNILength()
		;
	}
	
	private function getNILength(){
		return $this->bars["NI"];
	}
	
	private function getFILength(){
		return $this->bars["FI"];
	}
	
	private function getIDLength(){
		return $this->bars["ID"]["length"];
	}
	
	public function getClubeID(){
		return $this->bars["CI"];
	}
	
	public function split($s){
		$patternCode =
		"(.{".strlen($this->getClubeID())."})".
		"(.{".$this->getIDLength()."})".
		"(.{".$this->getFILength()."})".
		"(.{".$this->getNILength()."})"
				;
				
				$a = preg_split("/$patternCode/i", $s, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
				return array(
						"ci" => $a[0],
						"id" => $a[1],
						"fi" => $a[2],
						"ni" => $a[3]
				);
	}
	
	public function decode($s){
		$a = $this->split($s);
		return array(
				"lg" => strlen($s),
				"split" => $a,
				"ci" => $a["ci"],
				"id" => base_convert($a["id"],36,10),
				"fi" => base_convert($a["fi"],36,10),
				"ni" => base_convert($a["ni"],36,10),
		);
	}
	
	public function encode($a){
		$fn = "0";
		if (isset($a["id"])):
		$fn = $a["id"];
		elseif (isset($a["fn"])):
		$aux = $this->getFirstTag("fn",$a["fn"]);
		$fn = $aux["id"];
		endif;
		
		$fi = fStrZero(0,$this->getFILength());
		if (isset($a["fi"])):
		$fi = fStrZero(base_convert($a["fi"],10,36),$this->getFILength());
		endif;
		
		$ni = fStrZero(0,$this->getNILength());
		if (isset($a["ni"])):
		$ni = fStrZero(base_convert($a["ni"],10,36),$this->getNILength());
		endif;
		
		return mb_strtoupper( $this->getClubeID() . $fn . $fi . $ni);
	}
	
	public function getAllTags(){
		return $this->bars["ID"]["types"];
	}
	
	public function getTagsTipo($tg,$vl){
		return array_filter( $this->getAllTags(), function($e) use($tg,$vl){
			return $e[$tg] == $vl;
		});
	}
	
	public function getFirstTag($tg,$vl){
		$arr = $this->getTagsTipo($tg,$vl);
		reset($arr);
		return current($arr);
	}
	
	public function getTagByID($id){
		return $this->getFirstTag("id",$id);
	}
	
	public function has($tg,$vl){
		$arr = $this->getTagsTipo($tg,$vl);
		return (count($arr) > 0);
	}
}
?>