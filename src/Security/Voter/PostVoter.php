<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PostVoter extends Voter implements VoterInterface
{
    const string EDIT_POST = 'edit_post';
    const string DELETE_POST = 'delete_post';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT_POST, self::DELETE_POST])) {
            return false;
        }

        if (!$subject instanceof Post) {
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

        $post = $subject;
        return match ($attribute) {
            self::EDIT_POST => $this->canEdit($post, $user, $vote),
            self::DELETE_POST => $this->canDelete($post, $user, $vote),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEdit(Post $post, User $user, ?Vote $vote): bool {
        if ($user === $post->getCreatedBy()) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (email: %s) is not the author of this post (id: %d).',
            $user->getEmail(), $post->getId()
        ));

        return false;
    }

    private function canDelete(Post $post, User $user, ?Vote $vote): bool {

        if ($user === $post->getCreatedBy()) {
            return true;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (email: %s) is neither the author of this post (id: %d) or Administrator.',
            $user->getEmail(), $post->getId()
        ));

        return false;
    }
}
