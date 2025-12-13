<?php

namespace App\Tests\Unit\Service\Token;

use App\Entity\Token;
use App\Entity\User;
use App\Service\Token\TokenService;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class TokenServiceTest extends TestCase
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

    /**
     * @throws RandomException
     */
    public function testGenerateTokenValue(): void
    {
        $tokenValue = $this->tokenService->generateTokenValue();
        $this->assertisString($tokenValue, "Token is not a string");
        $this->assertSame(64, strlen($tokenValue), "Token length is not 64");
        $this->assertDoesNotMatchRegularExpression('/[^A-Za-z0-9\-._~]/', $tokenValue);
    }

    public function testGenerateToken(): void {
        $user = New User();
        $now = new \DateTimeImmutable();
        $token = $this->tokenService->generateToken($user);
        $expiredTime = new \DateTimeImmutable()->add(new \DateInterval('PT15M'));

        $this->assertInstanceOf(\DateTimeImmutable::class, $token->getExpiredAt());
        $this->assertGreaterThan($now, $token->getExpiredAt());
        $this->assertLessThanOrEqual($expiredTime, $token->getExpiredAt());
        $this->assertIsString($token->getValue());
        $this->assertSame($user, $token->getUser());
    }
}
