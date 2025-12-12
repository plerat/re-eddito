<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CommentVoter extends Voter implements VoterInterface
{
    const string EDIT_COMMENT = 'edit_comment';
    const string DELETE_COMMENT = 'delete_comment';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT_COMMENT, self::DELETE_COMMENT])) {
            return false;
        }

        if (!$subject instanceof Comment) {
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

        $comment = $subject;
        return match ($attribute) {
            self::EDIT_COMMENT => $this->canEdit($comment, $user, $vote),
            self::DELETE_COMMENT => $this->canDelete($comment, $user, $vote),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
    private function canEdit(Comment $comment, User $user, ?Vote $vote): bool {

        if ($user === $comment->getCreatedBy()) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (email: %s) is not the author of this comment (id: %d)',
            $user->getEmail(), $comment->getId()
        ));

        return false;
    }

    private function canDelete(Comment $comment, User $user, ?Vote $vote): bool {

        if ($user === $comment->getCreatedBy()) {
            return true;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        $vote?->addReason(sprintf(
            'The logged in user (email: %s) is neither the author of this comment (id: %d) or Administrator.',
            $user->getEmail(), $comment->getId()
        ));

        return false;
    }
}
