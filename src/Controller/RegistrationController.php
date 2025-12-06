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
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_send_verification_email', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('registration/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/register/send-verification-email/{id}', name: 'app_send_verification_email')]
    public function sendVerificationEmail(
        User $user,
        MailerService $mailerService,
        TokenService $tokenService,
        EntityManagerInterface $entityManager
    ): Response {
        $token = $tokenService->generateToken($user);
        $token->setUser($user);
        $entityManager->persist($token);
        $entityManager->flush();
        $mailerService->sendRegistrationEmail($user->getEmail(), $token);
        return $this->render('registration/send_verification_email.html.twig',[
            'user' => $user,
        ]);
    }

    #[Route('/register/validate/{value}', name: 'app_register_validate')]
    public function registerValidate(EntityManagerInterface $entityManager, Token $token): Response
    {
        $user = $token->getUser();
        if($token->getExpiredAt() < new \DateTime())
        {
            return $this->render('registration/expiratedToken.html.twig',[
                'user' => $user,
            ]);
        }
        $user->setIsEnabled(true);
        $entityManager->persist($user);
        $entityManager->remove($token);
        $entityManager->flush();

        #TODO: changer la route pour app_home, et login automatiquement le user
        return $this->redirectToRoute('app_login');
    }
}

