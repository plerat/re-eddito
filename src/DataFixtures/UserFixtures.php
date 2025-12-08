<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'admin@admin.xyz',
                'password' => 'admin',
                'pseudo' => 'admin',
                'roles' => 'ROLE_ADMIN',
                'enabled' => true,
            ],
            [
                'email' => 'user@user.xyz',
                'password' => 'user',
                'pseudo' => 'user',
                'roles' => 'ROLE_USER',
                'enabled' => true,
            ],
            [
                'email' => 'userNoEnabled@user.xyz',
                'password' => 'userNoEnabled',
                'pseudo' => 'userNoEnabled',
                'roles' => 'ROLE_USER',
                'enabled' => false,
            ]
        ];

        foreach ($usersData as $userdata) {
            $user = new User()
                ->setEmail($userdata['email'])
                ->setPseudo($userdata['pseudo'] )
                ->setPlainPassword($userdata['password'])
                ->setRoles([$userdata['roles']])
                ->setIsEnabled($userdata['enabled']);

            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
