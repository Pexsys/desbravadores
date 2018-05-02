<?php
global $mail;
@require_once("_core/lib/phpmailer/PHPMailerAutoload.php");
$GLOBALS['mail'] = new PHPMailer();
$GLOBALS['mail']->SetLanguage('br','phpmailer/language/');
$GLOBALS['mail']->IsSMTP();
$GLOBALS['mail']->Host = "mx1.hostinger.com.br";
$GLOBALS['mail']->Port = 465;
$GLOBALS['mail']->SMTPAuth = true;
$GLOBALS['mail']->SMTPSecure = 'ssl';
$GLOBALS['mail']->CharSet = "iso-8859-1";
//$GLOBALS['mail']->CharSet = "UTF-8";
$GLOBALS['mail']->Username = PATTERNS::getMail();
$GLOBALS['mail']->Password = "CVBpoi123";
$GLOBALS['mail']->IsHTML(true);
$GLOBALS['mail']->SetFrom(PATTERNS::getMail(), utf8_decode(PATTERNS::getClubeDS(array("cl","db","nm"))));
$GLOBALS['mail']->AddReplyTo(PATTERNS::getMail(), utf8_decode(PATTERNS::getClubeDS(array("cl","db","nm"))));
//$GLOBALS['mail']->Subject = "Contato ministeriosiasd.com.br";
?>