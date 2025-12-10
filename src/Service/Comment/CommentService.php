<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class CommentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function createCommentOnPost(Post $post, Comment $comment): void{
        $comment->setPost($post);
        $comment->setParent(null);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function createReplyOnComment(Comment $commentParent, Comment $replyComment): void{
        $replyComment->setParent($commentParent);
        $replyComment->setPost($commentParent->getPost());
        $this->entityManager->persist($replyComment);
        $this->entityManager->flush();
    }
}
