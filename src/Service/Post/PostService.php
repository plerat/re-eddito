<?php

namespace App\Service\Post;

use App\Entity\User;
use App\Repository\PostRepository;

class PostService
{
    public function __construct(
        private PostRepository $postRepository,
    ) {}

    public function retrieveAllPostFromUser(User $user): array
    {
        return $this->postRepository
            ->findBy(
            [
                'createdBy' => $user,
                'isDeleted'=> false
            ],
            ['id' => 'DESC']);
    }

    public function retrieveAllPosts(): array
    {
        return $this->postRepository
            ->findBy(
            [
                'isDeleted'=> false
            ],
            ['id' => 'DESC']);
    }
}
