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

final class UserSettingsController extends AbstractController
{

    protected ?Tools $tools;
    protected EntityManagerInterface $em;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager)
    {

        $this->em = $entityManager;
        $this->tools = new Tools;

    }

    #[Route('/settings', name: 'app_user_settings')]
    public function userSettingsNoLocal(): Response
    {
        return $this->redirectToRoute('app_user_settings', ['_locale' => 'en']);
    }

    #[Route('/{_locale<%app.supported_locales%>}user/settings', name: 'app_user_settings')]
    public function index(): Response
    {
        return $this->render('user_settings/index.html.twig', [
            'controller_name' => 'UserSettingsController',
            'is_mobile'          => $this->tools->isMobile(),
        ]);
    }
}
