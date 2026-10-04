<?php

namespace App\Services;

use App\Repository\NavbarRepository;
use App\Repository\UsersRepository;

class UsersService
{
    private UsersRepository $userRepository;

    public function __construct(UsersRepository $u){
        $this->userRepository         = $u;
    }

    public function getAccUser(array $criteria): ?\App\Entity\Users
    {
        return $this->userRepository->findOneBy($criteria);
    }
}