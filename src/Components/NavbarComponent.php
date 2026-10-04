<?php

namespace App\Components;

use App\Entity\Navbar;
use App\Repository\NavbarRepository;
use App\Services\NavbarService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('navbar', template: '/components/navbar/navbar.html.twig')]
class NavbarComponent
{
    /* Navbar object array come from database */


    private NavbarService $nav_bar;
    private NavbarRepository $navbarRepository;

    public string $name;

    public function __construct(NavbarService $nb){
        $this->nav_bar         = $nb;
    }

    #[ArrayShape([
        'name' => "mixed",
        'is_default'            => "mixed",
        'items'                 => "mixed",
        'items_count'           => "integer",
        'items_color'           => "mixed",
        'bg_color'              => "mixed",
        'show_language'         => "mixed",
        'show_account_circular' => "mixed",
        'show_logo'             => "mixed",
        'logo_path'             => "mixed",
        'show_search'           => "mixed",
    ])]
    public function getNavbar(): ?Navbar
    {

        return $this->nav_bar->getNavbar($this->name);

    }
}
