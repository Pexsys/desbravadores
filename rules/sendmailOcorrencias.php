<?php
function sendOcorrenciaByID($ocorrenciaID, $nrEnviados){
	$rs = CONN::get()->Execute("
		SELECT DISTINCT co.CD, co.DH, co.TXT, lc.ID, lc.EMAIL
		  FROM LOG_MENSAGEM lc
	INNER JOIN CAD_OCORRENCIA co ON (co.ID = lc.ID_ORIGEM AND lc.TP = 'O')
		 WHERE co.ID = ?
 		   AND co.FG_PEND = 'N'
		   AND lc.DH_SEND IS NULL
		   AND lc.EMAIL IS NOT NULL
	  ORDER BY 1
	", array($ocorrenciaID) );
	if (!$rs->EOF):
	    $mail = MAIL::get();
		foreach($rs as $k => $l):
		    $mail->Subject = PATTERNS::getClubeDS( array("cl","nm") ) . " - Ocorrencia #".$l["CD"]." [".strftime("%d/%m/%Y",strtotime($l["DH"])) ."]";
		    $mail->ClearAllRecipients();
			$mail->AddAddress($l["EMAIL"]);
			$mail->MsgHTML($l["TXT"]);
            $mail->Send();
            ++$nrEnviados;
			CONN::get()->Execute("UPDATE LOG_MENSAGEM SET DH_SEND = NOW() WHERE ID = ?", array($l["ID"]) );
	    endforeach;
  endif;

  return $nrEnviados;
}
?>