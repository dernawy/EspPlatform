<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserPageController extends AbstractController
{
    #[Route('/userpage', name: 'app_user_page')]
    public function index(): Response
    {
        return $this->render('userpage/index.html.twig', [
            'controller_name' => 'UserPageController',
        ]);
    }
}
