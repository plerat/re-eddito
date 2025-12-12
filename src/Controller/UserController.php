<?php

namespace App\Controller;


use App\Form\ProfileType;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER', statusCode: 403)]
final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, UserService $userService): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $newPseudo = $form->get('pseudo')->getData();
            $plainPassword = $form->get('_password')->getData();

            if (!$userService->isValidPassword($user, $plainPassword)) {
                $error = 'Mot de passe incorrect.';
            } elseif ($userService->isPseudoTaken($newPseudo, $user)) {
                $error = 'Ce pseudo est déjà utilisé.';
            } else {
                $userService->updatePseudo($user, $newPseudo);
                return $this->redirectToRoute('app_profile');
            }
        }
        return $this->render('user/profile.html.twig', [
            'form' => $form,
            'error' => $error,
        ]);
    }
}
