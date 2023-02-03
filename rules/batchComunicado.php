<?php
@require_once("../include/functions.php");
@require_once("sendmailOcorrencias.php");

$mail = MAIL::get();

//SECRETARIA - COMUNICADOS - EMAILS NAO ENVIADOS
$nrEnviados = 0;
$mail->ClearAllRecipients();
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.01-Analisando comunicados não enviados...')");
$rs = CONN::get()->execute("
		SELECT DISTINCT cc.ID, cc.CD, cc.DH, cc.TXT
		  FROM LOG_MENSAGEM lc
	INNER JOIN CAD_COMUNICADO cc ON (cc.ID = lc.ID_ORIGEM AND lc.TP = 'C')
		 WHERE cc.FG_PEND = 'N'
		   AND (lc.DH_SEND IS NULL AND lc.DH_READ IS NULL)
		   AND lc.EMAIL IS NOT NULL 
	  ORDER BY 1
");
foreach($rs as $k => $l):
	$mail->Subject = PATTERNS::getClubeDS( array("cl","nm") ) . " - Comunicado #".$l["CD"]." [".strftime("%d/%m/%Y",strtotime($l["DH"])) ."]";
	$rs1 = CONN::get()->execute("
		SELECT *
		  FROM LOG_MENSAGEM
		 WHERE DH_SEND IS NULL
		   AND EMAIL IS NOT NULL
		   AND ID_ORIGEM = ?
		   AND TP = ?
      ORDER BY ID
	", array($l["ID"], "C"));
    if (!$rs1->EOF):
	
		$mail->MsgHTML($l["TXT"]);
		$nrControle = 10;
		foreach($rs1 as $k1 => $l1):
			$mail->addBCC($l1["EMAIL"]);
			$nrEnviados++;
			$nrControle++;
		    if ($nrControle == 10):
    		    try {
                   $mail->Send();
                } catch (Exception $e) {
                    echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                }
    		    $mail->ClearAllRecipients();
		        $nrControle = 0;
		    endif;
		endforeach;
		if ($nrControle > 0):
		    try {
               $mail->Send();
            } catch (Exception $e) {
                echo 'Exceção capturada: ',  $e->getMessage(), "\n";
            }
		    $mail->ClearAllRecipients();
		endif;
		    
		//ATUALIZA ENVIO
		CONN::get()->execute("
			UPDATE LOG_MENSAGEM
			SET DH_SEND = NOW()
			WHERE DH_SEND IS NULL
			AND EMAIL IS NOT NULL
			AND ID_ORIGEM = ?
			AND TP = ?
		", array($l["ID"], "C"));
	endif;
endforeach;
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.02-Comunicados enviados com sucesso ($nrEnviados)...')");

//SECRETARIA - OCORRENCIAS - EMAILS NAO ENVIADOS
$nrEnviados = 0;
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.03-Analisando ocorrencias não enviados...')");
$rs = CONN::get()->execute("
		SELECT DISTINCT co.ID, co.CD, co.DH, co.TXT
		  FROM LOG_MENSAGEM lc
	INNER JOIN CAD_OCORRENCIA co ON (co.ID = lc.ID_ORIGEM AND lc.TP = 'O')
		 WHERE co.FG_PEND = 'N'
		   AND lc.DH_SEND IS NULL
		   AND lc.EMAIL IS NOT NULL
	  ORDER BY 1
");
foreach ($rs as $l):
	$nrEnviados = sendOcorrenciaByID($l["ID"], $nrEnviados);
endforeach;
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.04-Ocorrencias enviadas com sucesso ($nrEnviados)...')");

exit;
?>
