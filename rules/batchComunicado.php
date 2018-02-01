<?php
@require_once("../include/functions.php");

//SECRETARIA - COMUNICADOS - EMAILS NAO ENVIADOS
$nrEnviados = 0;
CONN::get()->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.00-Analisando comunicados não enviados...')");
$rs = CONN::get()->Execute("
		SELECT DISTINCT cc.ID, cc.CD, cc.DH, cc.TXT
		  FROM LOG_MENSAGEM lc
	INNER JOIN CAD_COMUNICADO cc ON (cc.ID = lc.ID_ORIGEM AND lc.TP = 'C')
		 WHERE cc.FG_PEND = 'N'
		   AND (lc.DH_SEND IS NULL AND lc.DH_READ IS NULL)
		   AND lc.EMAIL IS NOT NULL
	  ORDER BY 1
");
foreach($rs as $k => $l):

	SENDMAIL::get()->Subject = PATTERNS::getClubeDS( array("cl","nm") ) . " - Comunicado #".$l["CD"]." [".strftime("%d/%m/%Y",strtotime($l["DH"])) ."]";

	$rs1 = CONN::get()->Execute("
		SELECT *
		  FROM LOG_MENSAGEM
		 WHERE DH_SEND IS NULL
		   AND EMAIL IS NOT NULL
		   AND ID_ORIGEM = ?
		   AND TP = ?
      ORDER BY ID
	", array($l["ID"], "C") );
	if ( !$rs1->EOF ):
		SENDMAIL::get()->ClearAllRecipients();
		foreach($rs1 as $k1 => $l1):
			//SENDMAIL::get()->AddAddress();
			SENDMAIL::get()->addBCC( $l1["EMAIL"] );
			$nrEnviados++;
		endforeach;

		SENDMAIL::get()->MsgHTML($l["TXT"]);
		SENDMAIL::get()->Send();
		//ATUALIZA ENVIO
		CONN::get()->Execute("
			UPDATE LOG_MENSAGEM
				SET DH_SEND = NOW()
				WHERE DH_SEND IS NULL
				AND EMAIL IS NOT NULL
				AND ID_ORIGEM = ?
				AND TP = ?
		", array( $l["ID"], "C") );
	endif;

endforeach;
CONN::get()->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.99-Comunicados enviados com sucesso ($nrEnviados)...')");


//SECRETARIA - OCORRENCIAS - EMAILS NAO ENVIADOS
$nrEnviados = 0;
CONN::get()->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.00-Analisando ocorrencias não enviados...')");
$rs = CONN::get()->Execute("
		SELECT DISTINCT co.ID, co.CD, co.DH, co.TXT
		  FROM LOG_MENSAGEM lc
	INNER JOIN CAD_OCORRENCIA co ON (co.ID = lc.ID_ORIGEM AND lc.TP = 'O')
		 WHERE co.FG_PEND = 'N'
		   AND lc.DH_SEND IS NULL
		   AND lc.EMAIL IS NOT NULL
	  ORDER BY 1
");
foreach ($rs as $l):
	SENDMAILOCORRENCIAS::sendOcorrenciaByID($l["ID"]);
	$nrEnviados++;
endforeach;
CONN::get()->Execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.99-Ocorrencias enviados com sucesso ($nrEnviados)...')");

exit;
?>
