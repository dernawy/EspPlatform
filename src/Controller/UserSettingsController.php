<?php

namespace App\Controller;

use App\Entity\Devices;
use App\Entity\SiteSettings;
use App\Entity\Templates;
use App\Entity\UserSettings;
use App\Helpers\Tools;
use App\Services\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class UserSettingsController extends AbstractController
{

    protected ?Tools $tools;
    protected EntityManagerInterface $em;
    private UsersService $userService;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager)
    {

        $this->tools       = new Tools;
        $this->em          = $entityManager;
        $this->userService = $us;

    }

    #[Route('/settings', name: 'app_user_settings')]
    public function userSettingsNoLocal(): Response
    {
        return $this->redirectToRoute('app_user_settings', ['_locale' => 'en']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{_locale<%app.supported_locales%>}/user/settings', name: 'app_user_settings')]
    public function index(): Response
    {

        $encoders    = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer  = new Serializer($normalizers, $encoders);

        if ($this->getUser()) {

            $user = $this->userService->getAccUser(['username' => $this->getUser()->getUserIdentifier()]);

            //get user settings
            $user_settings         = $this->em->getRepository(UserSettings::class)->findOneBy(['user' => $user]);

            $user_template = $this->em->getRepository(Templates::class)->findOneBy(['template_user' => $user]);

            //get site settings for this user
            $site_settings         = $this->em->getRepository(SiteSettings::class)->findOneBy(['site_settings_user' => $user]);

            $site_general_content  = $serializer->serialize($site_settings->getSiteSetting(), 'json');

            $site_style_content    = $serializer->serialize($user_settings->getUserStyle(), 'json');
            $site_profile_content  = $serializer->serialize($user_settings->getProfileSettings(), 'json');
            $site_network_content  = $serializer->serialize($user_settings->getNetworkSettings(), 'json');

            // get this user devices (if any)
            $user_devices = $this->em->getRepository(Devices::class)->findOneBy(['devices_user' => $user]);

            return $this->render('user_settings/index.html.twig', [

                'is_mobile'          => $this->tools->isMobile(),
                'site_settings' => [
                    'site_general'  => $serializer->decode($site_general_content, 'json'),
                    'site_style'    => $serializer->decode($site_style_content, 'json'),
                ],

                'user_settings' => [
                    'user_ip'    => $user_settings->getUserIp(),
                    'user_ipv6'  => $user_settings->getIpv6(),
                    'template' => [
                        'id'        => $site_settings->getTemplate()->getId(),
                        'active'    => $site_settings->getTemplate()->isTemplateActive(),
                        'name'      => $site_settings->getTemplate()->getTemplateName(),
                        'user'      => $site_settings->getTemplate()->getUser(),
                    ],
                    'profile'    => $serializer->decode($site_profile_content, 'json'),
                    'network'    => $serializer->decode($site_network_content, 'json'),
                ],
            ]);
        }

        return $this->render('user_settings/index.html.twig', [
            'controller_name' => 'UserSettingsController',
            'is_mobile'          => $this->tools->isMobile(),
        ]);
    }
}
