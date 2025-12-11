<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{

    public const string FIRST_POST_REFERENCE = 'firstPost';
    public const string SECOND_POST_REFERENCE = 'secondPost';

    public function load(ObjectManager $manager): void
    {

        $firstPost = new Post()
            ->setTitle('Les fausses moustaches ?')
            ->setContent("Est-ce que vous avez déjà essayé d'utiliser une fausse moustache pour changer d'identité ?")
            ->setCreatedBy($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $manager->persist($firstPost);
        $this->addReference(self::FIRST_POST_REFERENCE, $firstPost);

        $secondPost = new Post()
            ->setTitle('les chaises de bar')
            ->setContent("On est d'accord que les tonneaux, les tabourets et les chaises hautes, tout ça,
                c'est vraiment naze ? Je paye pas mon coca 3 euros pour être mal assis")
            ->setCreatedBy($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $this->addReference(self::SECOND_POST_REFERENCE, $secondPost);

        $manager->persist($secondPost);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
