<?php
@require_once("../include/sendmail.php");

function getConclusaoMsg( $p ){
	return str_replace( array("&lt;", "&gt;"), array("<", ">"), htmlentities("<p>Olá ".$p["np"].",<br/>
		<br/>
		Em nome do Clube Pioneiros, quero lhe agradecer pelo seu esforço e por mais esta etapa concluída.<br/>
		<br/>
		No intuito de melhorar cada dia mais os registros da secretaria, nosso sistema detectou automaticamente que você concluiu o <b>".$p["nm"]."</b>.<br/>
		<br/>
		Entre no sistema do clube (www.iasd-capaoredondo.com.br/desbravadores) e confira na opção <i>Minha Página / Meu Aprendizado</i>. Caso não consiga ou não tenha acesso, procure seu conselheiro(a), instrutor(a) ou a secretaria do clube.<br/>
		<br/>
		Fiquei orgulhoso ao saber que se tornou um".($p["sx"] == "F"?"a":"")." especialista nessa área. Isso é bom pra você e também para o clube. Meus Parabéns!<br/>
		<br/>
		<br/>
		MARANATA!
		<br/>
		<br/>
		Com carinho,<br/>
		<br/>
		".$p["nd"]."<br/>
		<small>Clube Pioneiros - IASD Capão Redondo<small>
		</p>", ENT_NOQUOTES, 'UTF-8', false));
}
//echo getConclusaoMsg( array( "np" => "Ricardo", "nm" => "MESTRADO", "sx" => "M", "nd" => "Ricardo Jonadabs C&eacute;sar" )  );

function sendMestradoByID($mestradoID){
	$rs = $GLOBALS['conn']->Execute("
			SELECT DISTINCT ta.*
			  FROM LOG_MENSAGEM lc
		INNER JOIN TAB_APRENDIZADO ta ON (ta.ID = lc.ID_ORIGEM AND lc.TP = 'M')
			 WHERE ta.ID = ?
 			   AND ta.FG_PEND = 'N'
			   AND lc.DH_SEND IS NULL
			   AND lc.EMAIL IS NOT NULL			
		  ORDER BY 1
	", array($mestradoID) );
	if (!$rs->EOF):
		$GLOBALS['mail']->Subject = "Clube Pioneiros - Aviso de Conclusão!";
		
		$rs1 = $GLOBALS['conn']->Execute("
			SELECT *
			  FROM LOG_MENSAGEM
			 WHERE DH_SEND IS NULL
			   AND EMAIL IS NOT NULL
			   AND ID_ORIGEM = ?
			   AND TP = ?
	      ORDER BY ID
		", array($l["ID"], "M") );
		
		if ( !$rs1->EOF ):
			foreach($rs1 as $l1):
				$GLOBALS['mail']->ClearAllRecipients();
				$GLOBALS['mail']->AddAddress( $l1["EMAIL"] );
				$GLOBALS['mail']->MsgHTML("");
					
				if ( $GLOBALS['mail']->Send() ):
					$nrEnviados++;
					//ATUALIZA ENVIO
					$GLOBALS['conn']->Execute("
						UPDATE LOG_MENSAGEM
						   SET DH_SEND = NOW()
						 WHERE ID = ?
						   AND TP = ?
					", array($l1["ID"], "M") );
				else:
					echo "email não enviado para ". $l1["EMAIL"];
				endif;
			
			endforeach;
		endif;
	endif;
}
?>