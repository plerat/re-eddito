<?php

namespace App\Controller;

use App\Form\LoginUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login',name: 'app_login')]
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        UserRepository $userRepository
    ):Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $sendVerificationEmail = $error instanceof CustomUserMessageAccountStatusException;
        if($sendVerificationEmail){
            $userId = $userRepository->findOneBy(['email' => $authenticationUtils->getLastUsername()])->getId();
        }
        $form = $this->createForm(LoginUserType::class)->handleRequest($request);
        return $this->render('login/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'userId' => $userId ?? null,
            'sendVerificationEmail' => $sendVerificationEmail,
        ]);
    }

}
