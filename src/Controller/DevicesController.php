<?php

namespace App\Controller;

use App\Entity\Devices;
use App\Entity\SiteSettings;
use App\Entity\Templates;
use App\Entity\UserSettings;
use App\Helpers\Tools;
use App\Services\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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

final class DevicesController extends AbstractController
{
    protected ?Tools $tools;
    protected EntityManagerInterface $em;
    private UsersService $userService;
    protected $show_navbar;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager)
    {

        $this->tools       = new Tools;
        $this->em          = $entityManager;
        $this->userService = $us;
        $this->show_navbar = '';

    }

    #[Route('/devices', name: 'app_devices')]
    public function devicesNoLocal(): Response
    {
        return $this->redirectToRoute('app_devices', ['_locale' => 'en']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{_locale<%app.supported_locales%>}/user/devices', name: 'app_devices')]
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

            if($user_devices != null){

                return $this->render('devices/index.html.twig', [

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

                    'devices' => [
                        'name'             => $user_devices->getName(),
                        'model'            => $user_devices->getModel(),
                        'revision'         =>$user_devices->getRevision(),
                        'cpu'              => $user_devices->getCpu(),
                        'flash_total_size' => $user_devices->getFlashTotalSize(),
                        'flash_used_size'  => $user_devices->getFlashUsedSize(),
                        'flash_free_size'  => $user_devices->getFlashFreeSize(),
                        'free_heap'        => $user_devices->getFreeHeap(),
                        'min_heap'         => $user_devices->getMinHeap(),
                        'has_psram'        => $user_devices->hasPsram(),
                        'psram_size'       => $user_devices->getPsramSize(),
                        'used_psram'       => $user_devices->getUsedPsram(),
                        'free_psram'       => $user_devices->getFreePsram(),
                        'sdk_version'      => $user_devices->getSdkVersion(),
                        'ap_ssid'          => $user_devices->getApSsid(),
                        'ap_ip'            => $user_devices->getApIp(),
                        'ap_netmask'       => $user_devices->getApNetmask(),
                        'ap_gateway'       => $user_devices->getApGateway(),
                    ]
                ]);
            }
        }

        $site_settings         = $this->em->getRepository(SiteSettings::class)->findOneBy(['site_settings_user' => $this->getUser()]);
        $user_settings         = $this->em->getRepository(UserSettings::class)->findOneBy(['user' => $user]);

        $site_general_content  = $serializer->serialize($site_settings->getSiteSetting(), 'json');
        $site_style_content    = $serializer->serialize($user_settings->getUserStyle(), 'json');

        return $this->render('devices/index.html.twig', [
            'controller_name' => 'DevicesController',
            'is_mobile'          => $this->tools->isMobile(),
            'site_settings' => [
                'site_general'  => $serializer->decode($site_general_content, 'json'),
                'site_style'    => $serializer->decode($site_style_content, 'json'),
            ],
        ]);
    }
}
