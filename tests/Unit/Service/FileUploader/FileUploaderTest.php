<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\FileUploader;

use App\Service\FileUploader\MediaUploaderService;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class FileUploaderTest extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function testHandleMedia(): void
    {
        $sluggerStub = $this->createStub(SluggerInterface::class);
        $sluggerStub->method('slug')
            ->willReturnCallback(function ($string) {
                return new UnicodeString($string);
            });

        $filesystemMock = $this->createMock(Filesystem::class);
        $filesystemMock->method('exists')->willReturn(true);
        $filesystemMock->expects($this->never())->method('mkdir');

        $uploadedFileMock = $this->createMock(UploadedFile::class);
        $uploadedFileMock->method('getClientOriginalName')
            ->willReturn('my_image.jpg');
        $uploadedFileMock->method('guessExtension')
            ->willReturn('jpg');
        $uploadedFileMock->expects($this->once())
            ->method('move')
            ->with($this->equalTo('/fake/folder'));

        $uploaderService = new MediaUploaderService($sluggerStub, '/fake/folder', $filesystemMock);
        $result = $uploaderService->handleMedia($uploadedFileMock);

        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('type', $result);
        # Test here if slug + uniqID be applied to filename
        $this->assertNotEquals('my_image.jpg', $result['filename']);
        $this->assertStringEndsWith('.jpg', $result['filename']);
        $this->assertEquals('jpg', $result['type']);
    }
}
