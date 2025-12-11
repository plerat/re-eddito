<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{


    public function load(ObjectManager $manager): void
    {
        $firstComment = new Comment()
            ->setContent("Je connais un mec, Jeanno Mariuso, ça a trop marché pour lui, fonce")
            ->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class))
            ->setPost($this->getReference(PostFixtures::FIRST_POST_REFERENCE, Post::class));
        $manager->persist($firstComment);

        $SecondComment = new Comment()
            ->setContent("Oué t'as trop raison mec, je m'en sors pas avec mon gros cul")
            ->setCreatedBy($this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class))
            ->setPost($this->getReference(PostFixtures::SECOND_POST_REFERENCE, Post::class));
        $manager->persist($SecondComment);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PostFixtures::class
        ];
    }
}
