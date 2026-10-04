<?php

namespace App\Controller;

use App\Services\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Helpers\Tools;
use Symfony\Component\Routing\RouterInterface;

final class HomePageController extends AbstractController
{

    protected Tools $tools;
    protected ?Filesystem $filesystem;
    protected ?string $install_path;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager, ManagerRegistry $do){
        $this->tools                    = new Tools();
        $this->filesystem               = new Filesystem();
        $this->install_path             = 'installation/';

    }

    #[\Symfony\Component\Routing\Annotation\Route(path: '/', name: 'app_home_page')]
    public function homeNoLocale(): Response
    {

        if ($this->filesystem->exists($this->install_path)) {

            // TODO: Implement site security if already installed

            //return $this->redirectToRoute('app_installation');
        }

        return $this->redirectToRoute('app_home_page', ['_locale' => 'en']);
    }

    #[\Symfony\Component\Routing\Annotation\Route(path: '/{_locale<%app.supported_locales%>}/home', name: 'app_home_page')]
    public function index(): Response
    {
        return $this->render('home_page/index.html.twig', [
            'is_mobile'             => $this->tools->isMobile(),
        ]);
    }
}
