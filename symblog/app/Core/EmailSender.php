<?php 
namespace App\Core;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class EmailSender {

    function __construct() {
        $this->transport = Transport::fromDsn($_ENV['SMTP_SERVER']);
        $this->transport->setUsername($_ENV['SMTP_USER']);
        $this->transport->setPassword($_ENV['SMTP_PASSWORD']);
        $this->mailer = new Mailer($this->transport);
    }

    public function sendConfirmationMail($name, $surname, $email, $token) {
        $email = (new Email())
            ->from('a22ayrema@iesgrancapitan.org')
            ->to($email)
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Bienvenido a mi app de Porfolios!')
            ->text('Debes validar tu correo')
            ->html('<p>Haz click en el siguiente enlace para validar tu correo, tienes 24 horas!</p><br><a href="http://porfolio.local/verificar/'.$token.'">VALIDA TU CORREO</a>');

        $this->mailer->send($email);
    }
}