<?php

namespace App\Security\Voter;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MediaVoter extends Voter
{
    public const DELETE_MEDIA = 'delete_media';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::DELETE_MEDIA && $subject instanceof Media;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $media = $subject;

        // auteur du post
        if ($user === $media->getPost()->getCreatedBy()) {
            return true;
        }

        // admin
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
