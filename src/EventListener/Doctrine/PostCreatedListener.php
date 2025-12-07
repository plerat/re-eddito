<?php

namespace App\EventListener\Doctrine;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, entity: Post::class)]
readonly class PostCreatedListener
{
    public function __construct(
        private Security $security,
    )
    {

    }
    public function prePersist(Post $post): void
    {
        $user = $this->security->getUser();
        $post->setCreatedBy($user);
    }
}
