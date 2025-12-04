<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/test-mail')]
    public function testMail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('no-reply@toto.xyz')
            ->to('ton@adresse.fr')
            ->subject('Test Mailer')
            ->text('Ceci est un test');

        $mailer->send($email);

        return new Response('OK');
    }

}
