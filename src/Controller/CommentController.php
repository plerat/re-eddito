<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Security\Voter\CommentVoter;
use App\Service\Comment\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
final class CommentController extends AbstractController
{
    # add comment on a  post
    #[Route('/post/{id}/comment/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        PostRepository $postRepository,
        CommentService $commentService,
        int $id,
    ): Response
    {
        $post = $postRepository->find($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentService->createCommentOnPost($post, $comment);
            return $this->redirectToRoute('app_post_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
        return $this->render('comment/new.html.twig', [
            'form' => $form,
            'postId' => $id,
        ]);
    }


    #add a reply (comment) on a comment
    #[Route('/comment/{id}/reply', name: 'app_comment_reply', methods: ['GET', 'POST'])]
    public function reply(
        Request $request,
        Comment $commentParent,
        CommentService $commentService,
    ): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentService->createReplyOnComment($commentParent, $comment);
            return $this->redirectToRoute('app_comment_show', ['id' => $comment->getId()]);
        }

        return $this->render('comment/reply.html.twig', [
            'parentId' => $commentParent->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/comment/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(
        Comment $comment,
        CommentService $commentService
    ): Response {
        $postId = $comment->getPost()->getId();

        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
            'children' => $commentService->getVisibleChildren($comment),
            'postId' => $postId,
        ]);
    }


    #[Route('/comment/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    #[IsGranted(CommentVoter::EDIT_COMMENT, 'comment')]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $postId = $comment->getPost()->getId();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_post_show', ['id' => $postId,]);
        }
        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
            'postId' => $postId,
        ]);
    }

    #[Route('comment/{id}', name: 'app_comment_delete', methods: ['POST'])]
    #[IsGranted(CommentVoter::DELETE_COMMENT, 'comment')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $comment->setIsDeleted(true);
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        $parent = $comment->getParent();
        if (in_array('ROLE_ADMIN',$this->getUser()->getRoles())){
            return $this->render('admin/index.html.twig');
        }
        if ($parent !== null) {
            return $this->redirectToRoute('app_comment_show', ['id' => $parent->getId(),]);
        }
        $postId = $comment->getPost()->getId();
        return $this->redirectToRoute('app_post_show', ['id' => $postId,]);
    }
}
