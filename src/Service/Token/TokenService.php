<?php

namespace App\Service\Token;

use App\Entity\Token;
use App\Entity\User;
use Random\RandomException;

class TokenService
{

    /**
     * @throws RandomException
     */
    public function generateTokenValue(int $length = 64): string
    {
        $token = bin2hex(random_bytes(floor($length/2)));
        return $token;
    }

    public function generateToken(User $user): Token
    {
        $token = new Token();
        $token
            ->setUser($user)
            ->setValue($this->generateTokenValue())
            ->setExpiredAt((new \DateTimeImmutable())->add(new \DateInterval('PT15M')));

        return $token;
    }

}
