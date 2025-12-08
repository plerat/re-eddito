<?php

namespace App\Controller;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class MediaController extends AbstractController
{

    #[Route('/medias/{id}', name: 'app_media_download', methods: ['GET'])]
    public function downloadMedia(Media $media,
                                  #[Autowire('%upload_media_directory%')]
                                  string $uploadMediaDirectory): BinaryFileResponse {
        $filePath = $uploadMediaDirectory.$media->getName();
        return $this->file($filePath);
    }

    #[Route('/medias/delete/{id}', name: 'app_media_delete', methods: ['POST'])]
    public function deleteMedia(
        Request $request,
        Media $media,
        EntityManagerInterface $entityManager,
        #[Autowire('%upload_media_directory%')]
        string $uploadMediaDirectory
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$media->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->beginTransaction();
            try {
                $entityManager->remove($media);
                $entityManager->flush();
                $filePath = $uploadMediaDirectory . $media->getName();
                unlink($filePath);
            } catch (\Exception $exception) {
                $entityManager->rollBack();
                throw new RuntimeException("An error occurred during the deletion", $exception->getCode(), $exception);
            }
            $entityManager->commit();
        }
        return $this->redirectToRoute('app_post_edit', ['id'=> $media->getPost()->getId()], Response::HTTP_SEE_OTHER);
    }
}
