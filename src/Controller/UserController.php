<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ProfileType;
use App\Service\Comment\CommentService;
use App\Service\Post\PostService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER', statusCode: 403)]
final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_profile')]
    public function profile(
        User $user,
        Request $request,
        UserService $userService,
        PostService $postService,
        CommentService $commentService
    ): Response {
        $currentUser = $this->getUser();
        $isOwner = $currentUser && $currentUser->getId() === $user->getId();

        $form = null;
        $error = null;

        if ($isOwner) {
            $form = $this->createForm(ProfileType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newPseudo = $form->get('pseudo')->getData();
                $plainPassword = $form->get('_password')->getData();

                if (!$userService->isValidPassword($user, $plainPassword)) {
                    $error = 'Mot de passe incorrect.';
                } elseif ($userService->isPseudoTaken($newPseudo, $user)) {
                    $error = 'Ce pseudo est déjà utilisé.';
                } else {
                    $userService->updatePseudo($user, $newPseudo);

                    return $this->redirectToRoute('app_user_profile', [
                        'id' => $user->getId(),
                    ]);
                }
            }
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'isOwner' => $isOwner,
            'form' => $form,
            'error' => $error,
            'posts' => $postService->getAllPostFromUser($user),
            'comments' => $commentService->getUserComments($user),
        ]);
    }
}
