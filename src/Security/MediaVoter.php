<?php

namespace App\Security;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

class MediaVoter
{
    const string DELETE_MEDIA = 'delete_media';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::DELETE_MEDIA])) {
            return false;
        }

        if (!$subject instanceof Media) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            $vote?->addReason('The user is not logged in.');
            return false;
        }

        $media = $subject;
        return match ($attribute) {
            self::DELETE_MEDIA => $this->canDelete($media, $user, $vote),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canDelete(Media $media, User $user, ?Vote $vote): bool {

        if ($user ===  $media->getPost()->getCreatedBy()) {
            return true;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (email: %s) is neither the author of this media (id: %d) or Administrator.',
            $user->getEmail(), $media->getId()
        ));

        return false;
    }
}
