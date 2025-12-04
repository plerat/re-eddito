<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\RegisterUserType;
use App\Service\Mailer\MailerService;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        MailerService $mailerService,
        TokenService $tokenService
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $token = $tokenService->generateToken($user);
            $user->addToken($token);
            $entityManager->persist($user);
            $entityManager->flush();
            $mailerService->sendRegistrationEmail($user->getEmail(),$token);

            return $this->redirectToRoute('app_register'); #TODO: ajouter la validation du mail ici
        }

        return $this->render('registration/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/register/validate/{value}', name: 'app_register_validate')]
    public function registerValidate(EntityManagerInterface $entityManager, Token $token): Response
    {
        if($token->getExpiredAt() < new \DateTime())
        {
            $this->render('registration/expiratedToken.html.twig',[]);
        }
        $user = $token->getUser();
        $user->setIsEnabled(true);
        $entityManager->persist($user);
        $entityManager->remove($token);
        $entityManager->flush();

        #TODO: changer la route pour app_home, et login automatiquement le user
        return $this->redirectToRoute('app_login');

    }
}

