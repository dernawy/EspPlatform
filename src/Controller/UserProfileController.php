<?php

namespace App\Controller;

use App\Helpers\Tools;
use App\Services\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

final class UserProfileController extends AbstractController
{

    protected ?Tools $tools;
    protected EntityManagerInterface $em;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager)
    {

        $this->em = $entityManager;
        $this->tools = new Tools;

    }

    #[Route('/profile', name: 'app_user_profile')]
    public function profileNoLocal(): Response
    {
        return $this->redirectToRoute('app_user_profile', ['_locale' => 'en']);
    }

    #[Route('/{_locale<%app.supported_locales%>}/user/profile', name: 'app_user_profile')]
    public function index(): Response
    {
        return $this->render('user_profile/index.html.twig', [
            'controller_name' => 'UserProfileController',
            'is_mobile'          => $this->tools->isMobile(),
        ]);
    }
}
