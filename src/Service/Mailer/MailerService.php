<?php

namespace App\Service\Mailer;

use App\Entity\Token;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerService
{
    private MailerInterface $mailer;
    private Environment $twig;
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendRegistrationEmail(string $to, Token $token)
    {
        $template = $this->twig->load('email/registration.html.twig');

        $email = (new Email())
            ->from('no-reply@toto.xyz')
            ->to($to)
            ->subject($template->renderBlock('subject', ['token' => $token]))
            ->text($template->renderBlock('text', ['token' => $token]))
            ->html($template->renderBlock('html', ['token' => $token]));
        $this->mailer->send($email);
    }
}
