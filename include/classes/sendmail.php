<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MAILER_FACTORY {
  protected static $mail;

  function __construct() {
    if (!isset(self::$mail)) {
      self::$mail = new PHPMailer(true);
    //   self::$mail->SMTPDebug = SMTP::DEBUG_SERVER;
      self::$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      self::$mail->SetLanguage('br','phpmailer/language/');
      self::$mail->CharSet = "iso-8859-1";
      self::$mail->IsSMTP();
      self::$mail->SMTPAuth = true;
      self::$mail->Host = "smtp.hostinger.com.br";
      self::$mail->Port = 587;
      //self::$mail->CharSet = "UTF-8";
      self::$mail->Username = PATTERNS::getMail();
      self::$mail->Password = "CVBpoi123";
      self::$mail->IsHTML(true);
      self::$mail->SetFrom(PATTERNS::getMail(), utf8_decode(PATTERNS::getClubeDS(array("cl","db","nm"))));
      self::$mail->AddReplyTo(PATTERNS::getMail(), utf8_decode(PATTERNS::getClubeDS(array("cl","db","nm"))));
	}
  }

  public static function instance(){
		return new self();
	}
  
  public function getMail() {
    	return self::$mail;
    }
}

class MAIL {
	public static function get(){
		return MAILER_FACTORY::instance()->getMail();
	}
}
?>