<?php

namespace App\Tests\Unit\Service\Token;

use App\Service\Token\TokenService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TokenServiceTest extends KernelTestCase
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
