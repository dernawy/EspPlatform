<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SiteSettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_site_settings')]
    public function siteSettingsNoLocal(): Response
    {
        return $this->redirectToRoute('app_site_settings', ['_locale' => 'en']);
    }

    #[Route('/site/settings', name: 'app_site_settings')]
    public function index(): Response
    {
        return $this->render('site_settings/index.html.twig', [
            'controller_name' => 'SiteSettingsController',
        ]);
    }
}
