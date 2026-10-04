<?php

namespace App\Services;

use App\Repository\NavbarRepository;

class NavbarService
{
    private NavbarRepository $navbarRepository;

    public function __construct(NavbarRepository $nv){
        $this->navbarRepository         = $nv;
    }

    public function getDefaultNavbar()
    {
        return $this->navbarRepository->findOneBy(array('name'=> 'Default'));
    }

    public function getNavbar(string $navname)
    {
        return $this->navbarRepository->findOneBy(array('name'=> $navname));
    }
}