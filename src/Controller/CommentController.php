<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{

    # Display comment for one post
    #[Route('post/{id}/comment', name: 'app_comment_index', methods: ['GET'])]
    public function index(
        CommentRepository $commentRepository,
        PostRepository $postRepository,
        int $id,
    ): Response
    {
        $postId = $id;
        $post = $postRepository->find($postId);
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findBy([
                'post' => $postId,
                'parent' => null,
            ]),
            'post' => $post,
        ]);
    }


    # add comment on a  post
    #[Route('/post/{id}/comment/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PostRepository $postRepository,
        int $id,
    ): Response
    {
        $postId = $id;
        $post = $postRepository->find($postId);
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setParent(null);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [
                'id' => $postId,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
            'postId' => $postId,
        ]);
    }


    //TODO Faire le display pour un comment (afficher les commentaires d'un commentaire)
    #[Route('/comment/{id}', name: 'app_reply_index', methods: ['GET'])]
    public function index_reply(): Response {

    }

    #add a reply (comment) on a comment
    #[Route('/comment/{id}/reply', name: 'app_comment_reply', methods: ['GET', 'POST'])]
    public function reply(
        Request $request,
        Comment $commentParent,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $comment = new Comment();
        $comment->setParent($commentParent);
        $comment->setPost($commentParent->getPost());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comment/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        $postId = $comment->getPost()->getId();
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
            'postId' => $postId,
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('comment/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
