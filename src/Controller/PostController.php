<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Post;
use App\Form\PostNewType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader\MediaUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findBy(['isDeleted' => false], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaUploaderService $uploader
    ): Response
    {
        $post = new Post();
        $form = $this->createForm(PostNewType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # setCreatedAt manage in entity
            # setCreatedBy manage in eventListener
            $uploadedFile = $form->get('media')->getData();
            if ($uploadedFile instanceof UploadedFile) {
                $mediaData = $uploader->handleMedia($uploadedFile);
                $media = new Media();
                $media->setOriginalName($uploadedFile->getClientOriginalName());
                $media->setName($mediaData['filename']);
                $media->setType($mediaData['type']);
                $media->setPost($post);
                $entityManager->persist($media);
            }
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(
        Post $post,
        CommentRepository $commentRepository,
    ): Response
    {
        if ($post->getIsDeleted() | !$post) {
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }
        $comments = $commentRepository->findBy([
            'post' => $post->getId(),
            'parent' => null,
            'isDeleted' => false,
        ]);
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit
    (
        Request $request,
        Post $post,
        EntityManagerInterface $entityManager,
        MediaUploaderService $uploader
    ): Response
    {
        $form = $this->createForm(PostNewType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('media')->getData();
            if ($uploadedFile instanceof UploadedFile) {
                $mediaData = $uploader->handleMedia($uploadedFile);
                $media = new Media();
                $media->setOriginalName($uploadedFile->getClientOriginalName());
                $media->setName($mediaData['filename']);
                $media->setType($mediaData['type']);
                $media->setPost($post);
                $entityManager->persist($media);
            }
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_show', [
                'id' => $post->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete
    (
        Request $request,
        Post $post,
        EntityManagerInterface $entityManager,
        MediaUploaderService $uploader
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {

            $post->setIsDeleted(true);
            $entityManager->persist($post);
            $entityManager->flush();

            foreach ( $post->getMedias() as $media )
            {
                $uploader->unlinkMedia($media);
            }
        }
        if (in_array('ROLE_ADMIN',$this->getUser()->getRoles())){
            return $this->redirectToRoute('app_admin_post_list', [], Response::HTTP_SEE_OTHER);

        }
        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
