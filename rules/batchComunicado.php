<?php
@require_once("../include/functions.php");
@require_once("sendmailOcorrencias.php");
fConnDB();

//SECRETARIA - COMUNICADOS - EMAILS NAO ENVIADOS
$nrEnviados = 0;
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.00-Analisando comunicados não enviados...')");
$rs = $GLOBALS['conn']->Execute("
		SELECT DISTINCT cc.ID, cc.CD, cc.DH, cc.TXT
		  FROM LOG_MENSAGEM lc
	INNER JOIN CAD_COMUNICADO cc ON (cc.ID = lc.ID_ORIGEM AND lc.TP = 'C')
		 WHERE cc.FG_PEND = 'N'
		   AND (lc.DH_SEND IS NULL AND lc.DH_READ IS NULL)
		   AND lc.EMAIL IS NOT NULL 
	  ORDER BY 1
");
foreach($rs as $k => $l):

	$GLOBALS['mail']->Subject = "Clube Pioneiros - Comunicado #".$l["CD"]." [".strftime("%d/%m/%Y",strtotime($l["DH"])) ."]";

	$rs1 = $GLOBALS['conn']->Execute("
		SELECT *
		  FROM LOG_MENSAGEM
		 WHERE DH_SEND IS NULL
		   AND EMAIL IS NOT NULL
		   AND ID_ORIGEM = ?
		   AND TP = ?
      ORDER BY ID
	", array($l["ID"], "C") );
	if ( !$rs1->EOF ):
		$GLOBALS['mail']->ClearAllRecipients();
		foreach($rs1 as $k1 => $l1):
			//$GLOBALS['mail']->AddAddress();
			$GLOBALS['mail']->addBCC( $l1["EMAIL"] );
			$nrEnviados++;
		endforeach;

		$GLOBALS['mail']->MsgHTML($l["TXT"]);
		//if ( $GLOBALS['mail']->Send() ):
			//ATUALIZA ENVIO
			$GLOBALS['conn']->Execute("
				UPDATE LOG_MENSAGEM
				   SET DH_SEND = NOW()
				 WHERE DH_SEND IS NULL
		           AND EMAIL IS NOT NULL
				   AND ID_ORIGEM = ?
				   AND TP = ?
			", array( $l["ID"], "C") );
		//else:
			echo "email não enviado";
		//endif;
	endif;
	
endforeach;
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.99-Comunicados enviados com sucesso ($nrEnviados)...')");


//SECRETARIA - OCORRENCIAS - EMAILS NAO ENVIADOS
$nrEnviados = 0;
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.00-Analisando ocorrencias não enviados...')");
$rs = $GLOBALS['conn']->Execute("
		SELECT DISTINCT co.ID, co.CD, co.DH, co.TXT
		  FROM LOG_MENSAGEM lc
	INNER JOIN CAD_OCORRENCIA co ON (co.ID = lc.ID_ORIGEM AND lc.TP = 'O')
		 WHERE co.FG_PEND = 'N'
		   AND lc.DH_SEND IS NULL
		   AND lc.EMAIL IS NOT NULL
	  ORDER BY 1
");
foreach ($rs as $l):
	sendOcorrenciaByID($l["ID"]);
	$nrEnviados++;
endforeach;
$GLOBALS['conn']->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.99-Ocorrencias enviados com sucesso ($nrEnviados)...')");

exit;
?>