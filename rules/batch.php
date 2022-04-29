<?php
@require_once("../include/functions.php");
@require_once("../include/_message.php");

$today = getdate();
$mail = MAIL::get();

//******* INICIO DA ROTINA DIARIA
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.00-Iniciando rotina...')");

//******* LOG
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.01.00-Inicio do tratamento de LOGs...')");
CONN::get()->execute("DELETE FROM LOG_BATCH WHERE DH < ( NOW() - INTERVAL 30 DAY )");
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.01.99-Exclusão de LOGs antigos concluída.')");

//******* SECRETARIA
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.00-Analisando Secretaria...')");

$rDIR = CONN::get()->execute("SELECT * FROM CON_DIRETOR");
$nomeDiretor = titleCase($rDIR->fields["NOME_DIRETOR"]);

//******* SECRETARIA - FELIZ ANIVERSADIO
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.01-Analisando Aniversário...')");
	$rA = CONN::get()->execute("
		SELECT NM, TP_SEXO, Year(NOW())-Year(DT_NASC) AS IDADE_ANO, EMAIL
		  FROM CAD_PESSOA
		 WHERE DT_NASC IS NOT NULL
		  AND EMAIL IS NOT NULL
		  AND MONTH(DT_NASC) = MONTH(NOW())
		  AND DAY(DT_NASC) = DAY(NOW())
		  AND ID != ?
	", array($rDIR->fields["ID_DIRETOR"]) );
	foreach ($rA as $lA => $fA):
		$a = explode(" ",titleCase($fA["NM"]));

		$message = new MESSAGE( array( "np" => $a[0], "id" => $fA["IDADE_ANO"], "sx" => $fA["TP_SEXO"], "nd" => $nomeDiretor ) );
		$bm = $message->getBirthday();

		$mail->ClearAllRecipients();
		$mail->AddAddress( $fA["EMAIL"] );
		$mail->Subject = utf8_decode( $bm["sub"] );
		$mail->MsgHTML( $bm["msg"] );

		if ( $mail->Send() ):
			echo "parabens enviado para ". $fA["EMAIL"]."<br/>";
		else:
			echo "parabens não enviado para ". $fA["EMAIL"]."<br/>";
		endif;
	endforeach;

//******* SECRETARIA - MESTRADOS
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.02-Analisando Mestrados Completados...')");

  $rA = CONN::get()->execute("SELECT * FROM CON_ATIVOS");
  foreach ($rA as $lA => $fA):

    $a = explode(" ",titleCase($fA["NM"]));

      //LE REGRAS
    $rg = CONN::get()->execute("
        SELECT DISTINCT car.ID, car.CD_ITEM_INTERNO, car.CD_AREA_INTERNO, car.DS_ITEM, car.TP_ITEM, car.MIN_AREA
          FROM CON_APR_REQ car
          WHERE car.CD_AREA_INTERNO = 'ME'
      ORDER BY car.CD_ITEM_INTERNO
    ");
    foreach ($rg as $lg => $fg):
      $min = $fg["MIN_AREA"];

      $feitas = 0;
      //LE PARAMETRO MINIMO E HISTORICO PARA A REGRA
      $rR = CONN::get()->execute("
                SELECT tar.ID, tar.QT_MIN, COUNT(*) AS QT_FEITAS
                  FROM TAB_APR_ITEM tar
            INNER JOIN CON_APR_REQ car ON (car.ID_TAB_APR_ITEM = tar.ID AND car.TP_ITEM_RQ = 'ES')
            INNER JOIN APR_HISTORICO ah ON (ah.ID_TAB_APREND = car.ID_RQ AND ah.ID_CAD_PESSOA = ? AND ah.DT_CONCLUSAO IS NOT NULL)
                  WHERE tar.ID_TAB_APREND = ?
              GROUP BY tar.ID, tar.QT_MIN
      ", array($fA["ID_CAD_PESSOA"], $fg["ID"]) );
      foreach($rR as $lR => $fR):
        $feitas += min( $fR["QT_MIN"], $fR["QT_FEITAS"] );
      endforeach;

      $pct = floor( ( $feitas / $min ) * 100 );

      //VERIFICA SE COMPLETADO, MAS AINDA NÃO CONCLUIDO/AVALIADO/INVESTIDO
      if ( $pct >= 100 ):
            $rI = CONN::get()->execute("
                  SELECT DT_CONCLUSAO
                  FROM APR_HISTORICO
                  WHERE ID_CAD_PESSOA = ?
                    AND ID_TAB_APREND = ?
            ", array( $fA["ID_CAD_PESSOA"], $fg["ID"] ) );
            if ($rI->EOF || is_null($rI->fields["DT_CONCLUSAO"]) ):

                //INSERE NOTIFICAÇOES SE NÃO EXISTIR.
                CONN::get()->execute("
              INSERT INTO LOG_MENSAGEM ( ID_ORIGEM, TP, ID_CAD_USUARIO, EMAIL, DH_GERA )
              SELECT ?, 'M', cu.ID, ca.EMAIL, NOW()
                FROM CON_ATIVOS ca
            INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = ca.ID_CAD_PESSOA)
                WHERE ca.ID_CAD_PESSOA = ?
                  AND NOT EXISTS (SELECT 1 FROM LOG_MENSAGEM WHERE ID_ORIGEM = ? AND TP = 'M' AND ID_CAD_USUARIO = cu.ID)
            ", array( $fg["ID"], $fA["ID_CAD_PESSOA"], $fg["ID"] ) );

        if (!empty($fA["EMAIL"]) && $fA["ID_CAD_PESSOA"] != $rDIR->fields["ID_DIRETOR"] ):
          $message = new MESSAGE( array( "np" => $a[0], "nm" => $fg["DS_ITEM"], "sx" => $fA["SEXO"], "nd" => $nomeDiretor ) );

              $mail->ClearAllRecipients();
              $mail->AddAddress( $fA["EMAIL"] );
              $mail->Subject = utf8_decode(PATTERNS::getClubeDS( array("cl","nm") ) . " - Aviso de Conclusão");
              $mail->MsgHTML( $message->getConclusao() );

              if ( $mail->Send() ):
                $nrEnviados++;
                //ATUALIZA ENVIO
                CONN::get()->execute("
                  UPDATE LOG_MENSAGEM
                      SET DH_SEND = NOW()
                    WHERE ID = ?
                      AND TP = ?
                ", array( $l1["ID"], "M" ) );
                echo "email mestrado enviado para ". $fA["EMAIL"]."<br/>";
              else:
                echo "email mestrado não enviado para ". $fA["EMAIL"]."<br/>";
              endif;
            endif;
            endif;
      endif;
    endforeach;
  endforeach;

//*******  SECRETARIA - INATIVACAO DE MEMBROS COM PENDENCIAS CADASTRAIS
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('EMAILS','01.02.03-Analisando membros com pendências cadastrais...')");
$res = CONN::get()->execute("
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
	"
);
foreach($res as $k => $l):
	CONN::get()->execute("DELETE FROM CAD_ATIVOS WHERE NR_ANO = YEAR(NOW()) AND ID_CAD_MEMBRO = ?", array($l["ID"]) );
endforeach;

//*******  SECRETARIA - EXCLUSAO DE ACESSOS/PERFIS DE MEMBROS INATIVOS
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.04-Excluindo Perfis de Membros Inativos...')");
$result = CONN::get()->execute("
	  SELECT DISTINCT cu.ID
		FROM CAD_PESSOA cp
  INNER JOIN CAD_USUARIO cu ON (cu.ID_CAD_PESSOA = cp.ID)
  INNER JOIN CAD_USU_PERFIL cup ON (cup.ID_CAD_USUARIO = cu.ID)
	   WHERE NOT EXISTS (SELECT 1
							FROM CAD_ATIVOS a
					  INNER JOIN CAD_MEMBRO m ON (m.ID = a.ID_CAD_MEMBRO)
					  WHERE m.ID_CAD_PESSOA = cp.ID
						AND a.NR_ANO = YEAR(NOW())
						)
");
foreach($result as $l => $fields):
	PROFILE::deleteAllByUserID( $fields['ID'] );
endforeach;

//*******  SECRETARIA - REORGANIZACAO DA BASE EM 01/JANEIRO
if ($today["mon"] == 1 && $today["mday"] == 1):

	//BASE DE COMPRAS
	CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.05-Reorganizando base de compras...')");
	$result = CONN::get()->execute("
		SELECT *
		  FROM CAD_COMPRAS
		 WHERE FG_ENTREGUE = 'N'
	  ORDER BY ID_CAD_MEMBRO, ID_TAB_MATERIAIS, COMPL
	");
	CONN::get()->execute("TRUNCATE CAD_COMPRAS");
	foreach($result as $l => $f):
		CONN::get()->execute("
			INSERT INTO CAD_COMPRAS(
				ID_CAD_MEMBRO,
				ID_TAB_MATERIAIS,
				TP,
				COMPL,
				FG_COMPRA,
				FG_ENTREGUE,
				FG_PREVISAO
			) VALUES (?,?,?,?,?,?,?)
		", array(
			$f["ID_CAD_MEMBRO"],
			$f["ID_TAB_MATERIAIS"],
			$f["TP"],
			$f["COMPL"],
			$f["FG_COMPRA"],
			$f["FG_ENTREGUE"],
			$f["FG_PREVISAO"]
		));
	endforeach;

	//REQUISITOS ASSINADOS DE ITENS AINDA NÃO CONCLUÍDOS
	CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.06-Reorganizando base de requisitos assinados...')");
	CONN::get()->execute("DELETE FROM APR_PESSOA_REQ WHERE ID_HISTORICO IN (SELECT ID FROM APR_HISTORICO WHERE DT_CONCLUSAO IS NOT NULL)");
	$result = CONN::get()->execute("SELECT * FROM APR_PESSOA_REQ ORDER BY ID_HISTORICO, ID_TAB_APR_ITEM");
	CONN::get()->execute("TRUNCATE APR_PESSOA_REQ");
	foreach($result as $l => $f):
		CONN::get()->execute("
			INSERT INTO APR_PESSOA_REQ(
				ID_HISTORICO,
				ID_TAB_APR_ITEM,
				DT_ASSINATURA
			) VALUES (?,?,?)
		", array(
			$f["ID_HISTORICO"],
			$f["ID_TAB_APR_ITEM"],
			$f["DT_ASSINATURA"]
		));
	endforeach;
	
	
	//ALTERANDO IDADE DAS UNIDADES
	CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.09-Alterando idades das unidades...')");
	CONN::get()->execute("UPDATE TAB_UNIDADE SET IDADE = 9 WHERE IDADE = 15");
	CONN::get()->execute("UPDATE TAB_UNIDADE SET IDADE = (IDADE+1) WHERE IDADE < 16");


	//MENSAGEM DE FELIZ ANO NOVO
	CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.07-Felicitando pelo novo ano...')");
	$rA = CONN::get()->execute("
		SELECT NM, TP_SEXO, EMAIL
		  FROM CAD_PESSOA
		 WHERE EMAIL IS NOT NULL
		   AND ID != ?
	", array($rDIR->fields["ID_DIRETOR"]) );
	foreach ($rA as $lA => $fA):
		$a = explode(" ",titleCase($fA["NM"]));

		$message = new MESSAGE( array( "np" => $a[0], "id" => $fA["IDADE_ANO"], "sx" => $fA["TP_SEXO"], "nd" => $nomeDiretor ) );
		$bm = $message->getNewYear();

		$mail->ClearAllRecipients();
		$mail->AddAddress( $fA["EMAIL"] );
		$mail->Subject = utf8_decode( $bm["sub"] );
		$mail->MsgHTML( $bm["msg"] );

		if ( $mail->Send() ):
			echo "feliz ano novo enviado para ". $fA["EMAIL"]."<br/>";
		else:
			echo "feliz ano novo não enviado para ". $fA["EMAIL"]."<br/>";
		endif;
	endforeach;

//MENSAGEM DE FELIZ NATAL
elseif ($today["mon"] == 12 && $today["mday"] == 25):

	CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.08-Felicitando pelo natal...')");
	$rA = CONN::get()->execute("
		SELECT cp.NM, cp.TP_SEXO, EMAIL
			FROM CAD_PESSOA cp
			WHERE EMAIL IS NOT NULL
			AND ID != ?
	", array($rDIR->fields["ID_DIRETOR"]) );
	foreach ($rA as $lA => $fA):
		$a = explode(" ",titleCase($fA["NM"]));

		$message = new MESSAGE( array( "np" => $a[0], "id" => $fA["IDADE_ANO"], "sx" => $fA["TP_SEXO"], "nd" => $nomeDiretor ) );
		$bm = $message->getXMas();

		$mail->ClearAllRecipients();
		$mail->AddAddress( $fA["EMAIL"] );
		$mail->Subject = utf8_decode( $bm["sub"] );
		$mail->MsgHTML( $bm["msg"] );

		if ( $mail->Send() ):
			echo "feliz natal enviado para ". $fA["EMAIL"]."<br/>";
		else:
			echo "feliz natal não enviado para ". $fA["EMAIL"]."<br/>";
		endif;
	endforeach;
endif;

CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.02.99-Rotina de secretaria finalizada com Sucesso.')");

//******* TESOURARIA
//CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.03.00-Analisando Tesouraria...')");
//CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.03.99-Rotina de Tesouraria finalizada com Sucesso.')");

//******* FINAL DA ROTINA DIARIA
CONN::get()->execute("INSERT INTO LOG_BATCH(TP,DS) VALUES('DIÁRIA','01.99-Rotina finalizada com sucesso!')");
exit;
?>
