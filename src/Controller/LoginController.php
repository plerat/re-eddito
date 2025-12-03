<?php

namespace App\Controller;

use App\Form\LoginUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/login',name: 'app_login')]
    public function login(Request $request):Response
    {
        $form = $this->createForm(LoginUserType::class)->handleRequest($request);
        return $this->render('login/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
