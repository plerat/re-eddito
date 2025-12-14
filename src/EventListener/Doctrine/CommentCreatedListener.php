<?php

namespace App\EventListener\Doctrine;


use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: Comment::class)]
readonly class CommentCreatedListener
{
    public function __construct(
        private Security $security,
    )
    {

    }
    public function prePersist(Comment $comment): void
    {
        if ($comment->getCreatedBy() === null) {
            $user = $this->security->getUser();
            $comment->setCreatedBy($user);
        }
    }
}
