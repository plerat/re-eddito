<?php

namespace App\Tests\Unit\Service\Mailer;

use App\Entity\Token;
use App\Service\Mailer\MailerService;
use App\Service\Token\TokenService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailerServiceTest extends KernelTestCase
{
    private string $to;
    private Token $fakeToken;
    private MockObject $mailerInterfaceMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->to = 'test@test.xyz';
        $this->fakeToken = New Token();
        $this->mailerInterfaceMock = $this->createMock(MailerInterface::class);
    }

    protected function tearDown(): void
    {
        unset($to);
        unset($fakeToken);
        unset($mailerInterfaceMock);
    }

    public function testSendRegistrationMail(): void
    {
        $this->mailerInterfaceMock
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));

        $serviceMailer = new MailerService($this->mailerInterfaceMock);
        $serviceMailer->sendRegistrationEmail($this->to, $this->fakeToken);
    }

    public function testSendResetPasswordEmail(): void
    {
        $this->mailerInterfaceMock
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));

        $serviceMailer = new MailerService($this->mailerInterfaceMock);
        $serviceMailer->sendResetPasswordEmail($this->to, $this->fakeToken);
    }
}
