<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class UserFixtures extends Fixture
{
    public const string USER_REFERENCE = 'user';
    public const string ADMIN_USER_REFERENCE = 'admin';
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher)
    {}

    public function load(ObjectManager $manager): void
    {
        $adminUser = new User()
            ->setEmail('admin@admin.xyz')
            ->setPseudo('admin')
            ->setPlainPassword('admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsEnabled(true);

        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, $adminUser->getPlainPassword()));
        $manager->persist($adminUser);
        $this->addReference(self::ADMIN_USER_REFERENCE, $adminUser);

        $user = new User()
            ->setEmail('user@user.xyz')
            ->setPseudo('user')
            ->setPlainPassword('user')
            ->setRoles(['ROLE_USER'])
            ->setIsEnabled(true);

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE, $user);

        $shadowUser = new User()
            ->setEmail('shadow@shadow.xyz')
            ->setPseudo('shadow')
            ->setPlainPassword('shadow')
            ->setRoles(['ROLE_USER'])
            ->setIsEnabled(true);
        $shadowUser->setPassword($this->passwordHasher->hashPassword($shadowUser, $shadowUser->getPlainPassword()));
        $manager->persist($shadowUser);

        $manager->flush();
    }
}
