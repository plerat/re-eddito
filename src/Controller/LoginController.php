<?php

namespace App\Controller;

use App\Form\LoginUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login',name: 'app_login')]
    public function login(Request $request,AuthenticationUtils $authenticationUtils):Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $form = $this->createForm(LoginUserType::class)->handleRequest($request);
        return $this->render('login/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
        #TODO si le user a isEnabled à False et qu'il n'a pas de token valide, renvoyer le mail sendRegistrationEmail
    }

}
