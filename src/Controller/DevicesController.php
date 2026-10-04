<?php

namespace App\Controller;

use App\Helpers\Tools;
use App\Services\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

final class DevicesController extends AbstractController
{
    protected ?Tools $tools;
    protected EntityManagerInterface $em;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager)
    {

        $this->em = $entityManager;
        $this->tools = new Tools;

    }

    #[Route('/devices', name: 'app_devices')]
    public function devicesNoLocal(): Response
    {
        return $this->redirectToRoute('app_devices', ['_locale' => 'en']);
    }

    #[Route('/{_locale<%app.supported_locales%>}/user/devices', name: 'app_devices')]
    public function index(): Response
    {
        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
            'is_mobile'          => $this->tools->isMobile(),
        ]);
    }
}
