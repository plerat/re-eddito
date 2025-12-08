<?php

namespace App\Tests\Unit\Service\Token;

use App\Entity\Token;
use App\Entity\User;
use App\Service\Token\TokenService;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;


class TokenServiceTest extends WebTestCase
{
    private TokenService $tokenService;

    protected function setUp(): void
    {
        $this->tokenService = new TokenService();
    }

    protected function tearDown(): void
    {
        unset($this->tokenService);
    }

    public function testGenerateTokenValue(): void
    {
        $tokenValue = $this->tokenService->generateTokenValue();
        $this->assertisString($tokenValue, "Token is not a string");
        $this->assertSame(64, strlen($tokenValue), "Token length is not 64");
        $this->assertDoesNotMatchRegularExpression('/[^A-Za-z0-9\-._~]/', $tokenValue);
    }
}
