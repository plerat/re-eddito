<?php

namespace App\Service\FileUploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaUploaderService {
    private string $uploadMediaDirectory;
    private SluggerInterface $slugger;
    public function __construct(
        SluggerInterface $slugger,
        string $uploadMediaDirectory,
    )
    {
        $this->slugger = $slugger;
        $this->uploadMediaDirectory = $uploadMediaDirectory;
    }

    public function handleMedia(UploadedFile $file): array {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileType = $file->guessExtension();
        $newFilename = $safeFilename.'-'.uniqid().'.'.$fileType;
        $file->move($this->uploadMediaDirectory, $newFilename);
        return [
            'filename' => $newFilename,
            'type' => $fileType,
        ];
    }
}
