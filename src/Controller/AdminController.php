<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Comment\CommentService;
use App\Service\Post\PostService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', message: 'Access denied', statusCode: 403)]
final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig',);
    }
    #[Route('/admin/users', name: 'app_admin_user_list')]
    public function userList(UserService $userService): Response
    {
        $users = $userService->retrieveAllUsers();
        return $this->render('admin/user/users.html.twig',["users"=>$users]);
    }
    #[Route('/admin/user/{id}/post', name: 'app_admin_user_post')]
    public function adminUserPost(PostService $postService, User $user): Response
    {
        $post = $postService->getAllPostFromUser($user);
        return $this->render('admin/user/userPosts.html.twig',
            [
                'user'=>$user,
                'posts'=>$post,
            ]);
    }
    #[Route('/admin/user/{id}/comment', name: 'app_admin_user_comment')]
    public function userProfile(CommentService $commentService, User $user): Response
    {
        $comments = $commentService->getUserComments($user);
        return $this->render('admin/user/userComments.html.twig',
            [
                'user'=>$user,
                'comments'=>$comments
            ]);
    }

    #[Route('/admin/posts', name: 'app_admin_post_list')]
    public function postList(PostService $postService): Response
    {
        $post = $postService->retrieveAllPosts();
        return $this->render('admin/posts.html.twig',["posts"=>$post]);
    }
    #[Route('/admin/comments', name: 'app_admin_comment_list')]
    public function commentList(CommentService $commentService): Response
    {
        $comment = $commentService->getAllComments();
        return $this->render('admin/comment/comments.html.twig',["comments"=>$comment]);
    }




}
