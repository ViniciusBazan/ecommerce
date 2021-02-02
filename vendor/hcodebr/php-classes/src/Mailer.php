<?php

namespace Hcode;

use Rain\Tpl;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer {

    const USERNAME  = "faebulare@gmail.com";
    const PASSWORD  = "dhjv123456";
    const NAME_FROM = "Hcode Store";

    private $mail;

    public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
    {

        $config = array(
            "tpl_dir"       => $_SERVER['DOCUMENT_ROOT']."/views/email/",
            "cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
            "debug"         => false
           );

        Tpl::configure( $config );

        // create the Tpl object
        $tpl = new Tpl;

        foreach ($data as $key => $value) {
            $tpl->assign($key, $value);
        }

        $html = $tpl->draw($tplName, true);

        /**
         * This example shows settings to use when sending via Google's Gmail servers.
         * This uses traditional id & password authentication - look at the gmail_xoauth.phps
         * example to see how to use XOAUTH2.
         * The IMAP section shows how to save this message to the 'Sent Mail' folder using IMAP commands.
         */

        //Import PHPMailer classes into the global namespace



        //Create a new PHPMailer instance
        $this->mail = new \PHPMailer;

        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP(); // prepara para enviar email

        //Enable SMTP debugging
        // SMTP::DEBUG_OFF = off (for production use)
        // SMTP::DEBUG_CLIENT = client messages
        // SMTP::DEBUG_SERVER = client and server messages
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;

        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.gmail.com';
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = 587;

        $this->mail->SMTPOptions = array('ssl'=>array('verify_peer'=>false, 'verify_peer_name'=>false, 'allow_self_signed'=>true)); 

        //Set the encryption mechanism to use - STARTTLS or SMTPS
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail

        /* Nesse momento deve-se colocar email e senha do gmail */

        $this->mail->Username = Mailer::USERNAME;

        //Password to use for SMTP authentication
        $this->mail->Password = Mailer::PASSWORD;

        //Set who the message is to be sent from

        $this->mail->CharSet = 'UTF-8';
        $this->mail->Encoding = 'base64';

        /* Nesse momento definimos email e nome de quem envia */

        $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

        //Set an alternative reply-to address
        //Metodo não obrigatorio
        //$mail->addReplyTo('replyto@example.com', 'First Last');

        //Set who the message is to be sent to

        /* Define quem recebe os email, podendo colcoar multiplos destinatarios ao adicionar mais linhas com addAddress */

        $this->mail->addAddress($toAddress, $toName);

        //Set the subject line

        /* Define o assunto do email */

        $this->mail->Subject = $subject;

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body

        /* Define o corpo do email em um arquivo */

        $this->mail->msgHTML($html);

        //Replace the plain text body with one created manually

        //Define a mensagem que aparece caso o HTML não funcione

        $this->mail->AltBody = 'Texto alternativo';

        //Attach an image file

        /* Metodo comentado abaixo anexa algo */

        //$mail->addAttachment('images/phpmailer_mini.png');
    }

    public function send()
    {

        return $this->mail->send();

    }

    //Section 2: IMAP
        //IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
        //Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
        //You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
        //be useful if you are trying to get this working on a non-Gmail IMAP server.
        public function save_mail($mail)
        {
            //You can change 'Sent Mail' to any other folder or tag
            $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

            //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
            $imapStream = imap_open($path, $this->mail->Username, $mail->Password);

            $result = imap_append($imapStream, $path, $this->mail->getSentMIMEMessage());
            imap_close($imapStream);

            return $result;
        }

}

?>