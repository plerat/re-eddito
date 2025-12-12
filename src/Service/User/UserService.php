<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;


class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function retrieveAllUsers():array
    {
        return $this->userRepository->findAll();
    }
}
