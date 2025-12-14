<?php

namespace App\Service\FileUploader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Media;

class MediaUploaderService {

    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly string           $uploadMediaDirectory,
        private readonly Filesystem       $filesystem,
    )
    {}

    public function handleMedia(UploadedFile $file): array {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileType = $file->guessExtension();
        $newFilename = $safeFilename.'-'.uniqid().'.'.$fileType;
        if (!$this->filesystem->exists($this->uploadMediaDirectory)) {
            $this->filesystem->mkdir($this->uploadMediaDirectory);
        }
        $file->move($this->uploadMediaDirectory, $newFilename);
        return [
            'filename' => $newFilename,
            'type' => $fileType,
        ];
    }

    public function unlinkMedia(Media $file): void
    {
        unlink($this->uploadMediaDirectory.$file->getName());
    }
}
