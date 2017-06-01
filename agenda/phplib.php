<?php
	class clbd {
    var $Domain;
    var $SqlBanco;
    var $SqlDatabase;
    var $SqlUser;
    var $SqlPassWord;
    var $SqlHost;
		var $ASPDomain;
		var $PHPDomain;
    var $SqlHandle;
    var $SqlResult = array(0,0,0);
		var $SockServer;
		var $SockPort;
		var $DSN;

		function fInitServer($psSERVER_NAME){
			$this->Domain = "iasd-capaoredondo.com.br";
			$this->SqlHost = "localhost";
			$this->PHPDomain = "http://www." . $this->Domain;

			$this->SqlDatabase = "capao_pioneiros";
      $this->SqlUser = "capao_pioneiros";
      $this->SqlPassWord = "CVBpoi123";
			return "";
		}

   /*
   Novas Funções para acesso a Banco de Dados
   */
    function dbConecta(){ 
			$this->SqlHandle = mysql_connect($this->SqlHost,$this->SqlUser,$this->SqlPassWord) or $this->fErro('Erro ao Conectar com o Banco de dados - 100',0);
    }

    function dbDisconecta(){ 
			mysql_close($this->SqlHandle);
    }

    function dbErro( $MensErro,$gsComando){
		  $this->fErro( $MensErro,0);
	  }

    function dbQuery($gsComando,$cursor){	
			$this->SqlResult[$cursor] = mysql_db_query($this->SqlDatabase, $gsComando) or $this->dbErro('Erro ao Conectar com o Banco de dados - 200',$gsComando);
			if (StrToUpper($this->fStrLeft(trim($gsComando),6)) == 'SELECT' ):
				return mysql_num_rows($this->SqlResult[$cursor]);
			else:
				$return = mysql_affected_rows($this->SqlHandle);
				if ($return < 0):
					return 0;
				else:
					if ($return == 0):
						return 1;						
					else:
						return $return;
					endif;
				endif;
			endif;
  	}

    function dbFetch($cursor){
			$smtp = mysql_fetch_object($this->SqlResult[$cursor]); 
 			return $smtp;
  	}

    function dbGetHandleQuery($cursor){
			$smtp = $this->SqlResult[$cursor]; 
 			return $smtp;
  	}
    function dbGetHandleConnect(){
			$smtp = $this->SqlHandle;
 			return $smtp;
  	}

	  function fStrLeft($str,$nChar){
			return substr($str,0,$nChar);
		}

	  function fStrRight($str,$nChar){
			return substr($str,strlen($str)-$nChar,$nChar);
		}

    function fSoNumero( $string1 ){
      $length=strlen($string1)-1;
      $string2='';
      $i=0;
      while ($i<=$length){
        $pos = $this->StrPos2( '0123456789', substr($string1,$i,1));
        if ($pos<0):
   			  $pos=0;
        else:
	        $string2 .= substr($string1,$i,1);
		    endif;
        $i++;
		  }
      return $string2;
    }

    function fStrZero( $num,$tam ){
      $resto=strlen( $num );
      $i=0;
      while ($i < $tam-$resto){
        $num='0'.$num;
        $i=$i+1;
      }
      return $num;
    }
    
    function fErro($txtErro,$Volta){
      if ($Volta)
        echo "<script language=javascript>alert('$txtErro');history.go(-1);</script>";
      else
        echo "<script language=javascript>alert('$txtErro');</script>";
    }

    function fCalcIdade($strData){
		  if ($strData != ''):
				$ano_nas = substr($strData,0,4);
				$mes_nas = substr($strData,5,2) . substr($strData,8,2);
				$ano_atu = date("Y");
				$mes_atu = date("m") . date("d");

				$idade = $ano_atu - $ano_nas;
				if ($mes_atu < $mes_nas):
					$idade -= 1;
				endif;
				return $idade;
		  else:
		    return 0;
		  endif;
		}

		function StrPos2($stringao, $string){
			$passo=strlen($string);
			$length=strlen($stringao)-$passo;
			$i=0;
			$found=0;
			while ($i <= $length)
			{
				if (substr($stringao,$i,$passo) == $string):
					$found=1;
					break;
				endif;
				$i=$i+1;		
			}
			if ($found==1):
				return $i;
			else:
				return -1;
			endif;		
		}

		function fDescMes($dData){
			$cRet = "";
			$cMes = substr($dData,3,2);
			if ($cMes == "01"):
				$cRet = "Janeiro";
			elseif ($cMes == "02"):
				$cRet = "Fevereiro";
			elseif ($cMes == "03"):
				$cRet = "Março";
			elseif ($cMes == "04"):
				$cRet = "Abril";
			elseif ($cMes == "05"):
				$cRet = "Maio";
			elseif ($cMes == "06"):
				$cRet = "Junho";
			elseif ($cMes == "07"):
				$cRet = "Julho";
			elseif ($cMes == "08"):
				$cRet = "Agosto";
			elseif ($cMes == "09"):
				$cRet = "Setembro";
			elseif ($cMes == "10"):
				$cRet = "Outubro";
			elseif ($cMes == "11"):
				$cRet = "Novembro";
			elseif ($cMes == "12"):
				$cRet = "Dezembro";
			endif;
			return $cRet;
		}

		function fNDiaDSemana($dData,$lConcatDia,$lConcatMes){
			$cDiaSemana = "";
			$cMes = substr($dData,5,2);
			$cDia = substr($dData,8,2);
			$data = $cMes . "/" . $cDia . "/" . substr($dData,0,4);
			$nDiaSemana = strftime("%w", strtotime($data)) + 1;
			if ($nDiaSemana == 1):
				$cDiaSemana = "Domingo";
			elseif ($nDiaSemana == 2):
				$cDiaSemana = "Segunda-Feira";
			elseif ($nDiaSemana == 3):
				$cDiaSemana = "Terça-Feira";
			elseif ($nDiaSemana == 4):
				$cDiaSemana = "Quarta-Feira";
			elseif ($nDiaSemana == 5):
				$cDiaSemana = "Quinta-Feira";
			elseif ($nDiaSemana == 6):
				$cDiaSemana = "Sexta-Feira";
			elseif ($nDiaSemana == 7):
				$cDiaSemana = "Sábado";
			endif;
			$lsRetorno = $cDiaSemana;
			if ($lConcatDia){
				$lsRetorno = $cDia . " " . $cDiaSemana;
			}
			if ($lConcatMes){
				$lsRetorno = $cDia . "/" . $cMes . " " . $cDiaSemana;
			}
			return $lsRetorno; 
		}

		function fRequest($cVAR){
			$cRet = $_GET["$cVAR"];
			if ($cRet == ""):
				$cRet = $_POST["$cVAR"];
			endif;
			return $cRet;
		}

		function fDescHora($cHora){
			$cHor = substr($cHora,0,2);
			$cMin = substr($cHora,3,2);
			$cRetorno = "";

			if ($cHor == "00"):
				if ($cMin > "00"):
					$cRetorno = $cHor. "h" . $cMin;
				endif;
			elseif ($cHor > "00"):
				$cRetorno = $cHor . "h";
				if ($cMin > "00"):
					$cRetorno = $cRetorno . $cMin;
				endif;
			endif;
			return $cRetorno;
		}

		function fFormatDataBanco($strData){
		  if ($strData != ''):
			  if ( $this->fSoNumero( substr($strData,11,8) ) != '' ):
			    return substr($strData,6,4). '-'.substr($strData,3,2).'-'.substr($strData,0,2).' '.substr($strData,11,8);
   			else:
	  		  return substr($strData,6,4). '-'.substr($strData,3,2).'-'.substr($strData,0,2);
		  	endif;
		  else:
		    return $strData;
		  endif;
		}

    function fFormatBancoData($strData){
		  if ($strData != ''):
				if ( $this->fSoNumero( substr($strData,11,8) ) != '' ):
					return substr($strData,8,2).'/'.substr($strData,5,2).'/'.substr($strData,0,4).' '.substr($strData,11,8);
				else:
					return substr($strData,8,2).'/'.substr($strData,5,2).'/'.substr($strData,0,4);
				endif;
		  else:
		    return $strData;
		  endif;
		}

		function fSetSockPar(){
			$this->SockServer = "pop." . $this->Domain;
			$this->SockPort = 110;
			$this->DSN = "{" . $this->Domain . ":" . $this->SockPort . "/pop3}";
      return "";
    }

		function fLoginUser($user, $pass){
			$lnReturn = -1;
			if ($this->SqlHost == "localhost"):
				$lnReturn = 1;
			else:
				/*
				$this->fSetSockPar();
				$connection = fsockopen($this->SockServer, $this->SockPort, $errno, $errstr, 30);
				if ($connection):
					$output = fgets($connection, 256);
					//echo "1: $output<br/>";
					$subout = substr($output, 0, 3);
					if ($subout == "+OK"):
						if ( $this->fStrRight($user,strlen($this->Domain) + 1) != "@". $dominio):
							$user .= "@". $this->Domain;
						endif;
						$lnReturn = 0;
						fputs($connection, "USER $user\r\n",strlen("USER $user\r\n"));
						$output = fgets($connection, 256);
						//echo "2: $output<br/>";
						$subout = substr($output, 0, 3);
						if ($subout == "+OK"):
							fputs($connection, "PASS $pass\r\n", strlen("PASS $pass\r\n"));
							$output = fgets($connection, 256);
							//echo "3: $output<br/>";
							$subout = substr($output, 0, 4);
							if ($subout == "+OK "):
								$lnReturn = 1;
							endif;
						endif;
						fputs($connection, "quit\r\n");
						fclose($connection);
					endif;
				endif;
				*/
				if ($user == "administrador" && $pass == "CVBpoi123"):
					$lnReturn = 1;
				endif;
			endif;
			return $lnReturn;
		}

		function date_diff($from, $to){  
			list($from_year, $from_month, $from_day ) = explode("-", $from); 
			list($to_year, $to_month, $to_day ) = explode("-", $to); 
							
			$from_date = mktime(0,0,0,$from_month,$from_day,$from_year); 
			$to_date = mktime(0,0,0,$to_month,$to_day,$to_year); 
							
			$days = ($to_date - $from_date)/86400; 

			/*Adicionado o ceil($days) para garantir que o resultado seja sempre um número inteiro */ 
			return ceil($days); 
		}  

		function fDiaMes($dData){
			$cDia = $this->fStrLeft($dData,2);
			return $cDia . "/" . $this->fStrEsp($this->fDescMes($dData));
		}

		function fTempo($data1,$hora1,$data2,$hora2){ 
			$i = split(":",$hora1); 
			$j = split("-",$data1); 
			$k = split(":",$hora2); 
			$l = split("-",$data2); 

			$tempo1 = mktime($i[0],$i[1],$i[2],$j[1],$j[2],$j[0]); 
			$tempo2 = mktime($k[0],$k[1],$k[2],$l[1],$l[2],$l[0]); 

			$tempo = $tempo2 - $tempo1; //ceil((($tempo2 - $tempo1)/60)/60); 
			return $tempo; 
		} 

		function fStrEsp($cString){
			$cString = str_replace("á", "á", $cString);
			$cString = str_replace("é", "é", $cString);
			$cString = str_replace("í", "í", $cString);
			$cString = str_replace("ó", "ó", $cString);
			$cString = str_replace("ú", "ú", $cString);
			$cString = str_replace("Á", "Á", $cString);
			$cString = str_replace("É", "É", $cString);
			$cString = str_replace("Í", "Í", $cString);
			$cString = str_replace("Ó", "Ó", $cString);
			$cString = str_replace("Ú", "Ú", $cString);
																										  
			$cString = str_replace("ç", "ç", $cString);
			$cString = str_replace("Ç", "Ç", $cString);
																										  
			$cString = str_replace("à", "à", $cString);
			$cString = str_replace("À", "À", $cString);
																										  
			$cString = str_replace("ã", "ã", $cString);
			$cString = str_replace("õ", "õ", $cString);
			$cString = str_replace("Ã", "Ã", $cString);
			$cString = str_replace("Õ", "Õ", $cString);
																										  
			$cString = str_replace("â", "â", $cString);
			$cString = str_replace("ê", "ê", $cString);
			$cString = str_replace("ô", "ô", $cString);
			$cString = str_replace("Â", "Â", $cString);
			$cString = str_replace("Ê", "Ê", $cString);
			$cString = str_replace("Ô", "Ô", $cString);

			$cString = str_replace("ü", "ü", $cString);
			$cString = str_replace("Ü", "Ü", $cString);

			$cString = str_replace("º", "º", $cString);
			$cString = str_replace("ª", "ª", $cString);
																											
			$cString = str_replace("\r\n", "<br>", $cString);

			return $cString;
		}

	}