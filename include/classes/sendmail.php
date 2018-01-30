<?php
class SENDMAIL {

    private static $mail;

    public function __construct(){
        if (!isset(self::$mail)) {
            self::$mail = PHPMailer();
            self::$mail->SetLanguage('br','phpmailer/language/');
            self::$mail->IsSMTP();
            self::$mail->Host = "mx1.hostinger.com.br";
            self::$mail->Port = 465;
            self::$mail->SMTPAuth = true;
            self::$mail->SMTPSecure = 'ssl';
            self::$mail->CharSet = "iso-8859-1";
            //self::$mail->CharSet = "UTF-8";
            self::$mail->Username = PATTERNS::getMail();
            self::$mail->Password = "CVBpoi123";
            self::$mail->IsHTML(true);
            self::$mail->SetFrom(PATTERNS::getMail(), utf8_decode(PATTERNS::getClubeDS(array("cl","db","nm"))));
            self::$mail->AddReplyTo(PATTERNS::getMail(), utf8_decode(PATTERNS::getClubeDS(array("cl","db","nm"))));
            //self::$mail->Subject = "Contato ministeriosiasd.com.br";
        }
    }

    public static function instance(){
		return new self();
	}

	public static function get() {
        if (!isset(self::$mail)) {
            SENDMAIL::instance();
        }
		return self::$mail;
	}
   
}
?>
