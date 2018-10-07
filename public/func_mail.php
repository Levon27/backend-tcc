<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envia_email($email,$nome,$mensagem){
	$mail = new PHPMailer(true);
		try {
			//Server settings
			$mail->SMTPDebug = 2;                                 // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'smtp-mail.outlook.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'sustek_tcc@outlook.com';      // SMTP username
			$mail->Password = 'sustek2018';                     // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 587;                                    // TCP port to connect to
			
			//Recipients
			$mail->setFrom('sustek_tcc@outlook.com', 'Mailer');
			$mail->addAddress($email, $nome);     // Add a recipient

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
			$message = file_get_contents('template.html');
			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'sustek esta lhe avisando';
			$mail->Body   = $message;
			

			$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}

	
	
	
}