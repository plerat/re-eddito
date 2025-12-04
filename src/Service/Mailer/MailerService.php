<?php

namespace App\Service\Mailer;

use App\Entity\Token;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class MailerService
{
    private MailerInterface $mailer ;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendRegistrationEmail(string $to, Token $token)
    {
        $email= (new TemplatedEmail())
            ->from('noreply@re-eddito.xyz')
            ->to($to)
            ->subject('Validate your account on Re-eddito')
            ->htmlTemplate('email/registration.html.twig')
            ->locale('fr')
            ->context([
                'token' => $token
            ]);
        $this->mailer->send($email);
    }
}
