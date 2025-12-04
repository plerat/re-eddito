<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class ConfirmEmailUserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && !$user->GetIsEnabled())
        {
            throw new CustomUserMessageAccountStatusException('Account is not Verified',$messageData=['Account is not Verified'],403);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }
}
