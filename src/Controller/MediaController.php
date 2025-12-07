<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
}
