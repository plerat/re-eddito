<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function retrieveAllUsers(): array
    {
        return $this->userRepository->findAll();
    }


    public function isPseudoTaken(string $pseudo, ?User $excludeUser = null): bool
    {
        $user = $this->userRepository->findOneBy(['pseudo' => $pseudo]);
        if (!$user) {
            return false;
        }
        if ($excludeUser && $user->getId() === $excludeUser->getId()) {
            return false;
        }
        return true;
    }

    public function isValidPassword(User $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }

    public function updatePseudo(User $user, string $newPseudo): void
    {
        $user->setPseudo($newPseudo);
        $this->entityManager->flush($user);
        $this->entityManager->persist($user);
    }
}
