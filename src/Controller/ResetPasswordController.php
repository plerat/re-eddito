<?php

namespace App\Controller;

use App\Entity\Token;
use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\Mailer\MailerService;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_request-forgot-password')]
    public function requestResetPassword(
        Request                $request,
        UserRepository         $userRepository,
        MailerService          $mailerService,
        TokenService           $tokenService,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email' => $form->getData()['email']]);

            if ($user) {
                $token = $tokenService->generateToken($user);
                $user->addToken($token);
                $entityManager->flush();
                $mailerService->sendResetPasswordEmail($user->getEmail(), $token);
            }
            # We don't send error if there is no user with this email to not give information
            $this->addFlash('Success', 'If your email is registered, please check your inbox.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('resetpassword/reset_password_request.html.twig', [
            'requestResetPasswordForm' => $form->createView()
        ]);
    }

    #[Route('/reset-password/{value}', name: 'app_reset-password')]
    public function resetPassword(
        Request $request,
        Token $token,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response
    {
        $user = $token->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user) {
                throw new NotFoundHttpException();
            }
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->setIsEnabled(true);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('Success', 'Password reset');
            return $this->redirectToRoute('app_login');

        }
        return $this->render('resetpassword/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView()
        ]);
    }
}
