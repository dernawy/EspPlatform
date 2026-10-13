<?php

namespace App\Controller;

use App\Entity\Devices;
use App\Entity\SiteSettings;
use App\Entity\Templates;
use App\Entity\UserSettings;
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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class HomePageController extends AbstractController
{

    protected Tools $tools;
    protected ?Filesystem $filesystem;
    protected ?string $install_path;

    protected EntityManagerInterface $em;
    private UsersService $userService;

    protected array $devices_array;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager){
        $this->tools                    = new Tools();
        $this->filesystem               = new Filesystem();
        $this->install_path             = 'installation/';
        $this->em                       = $entityManager;
        $this->userService              = $us;
        $this->devices_array            = [];

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

    /**
     * @throws ExceptionInterface
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{_locale<%app.supported_locales%>}/home', name: 'app_home_page')]
    public function index(): Response
    {

        if ($this->getUser()) {

            $user_array = $this->userService->getAccUser(['username' => $this->getUser()->getUserIdentifier()]);

            $encoders    = [new XmlEncoder(), new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];

            $serializer  = new Serializer($normalizers, $encoders);

            $site_settings         = $this->em->getRepository(SiteSettings::class)->findOneBy(['site_settings_user' => $user_array]);

            $site_general_content  = $serializer->serialize($site_settings->getSiteSetting(), 'json');

            $user_settings         = $this->em->getRepository(UserSettings::class)->findOneBy(['user' => $user_array]);

            $site_style_content    = $serializer->serialize($user_settings->getUserStyle(), 'json');
            $site_profile_content  = $serializer->serialize($user_settings->getProfileSettings(), 'json');
            $site_network_content  = $serializer->serialize($user_settings->getNetworkSettings(), 'json');

            $templates_settings = $this->em->getRepository(Templates::class)->findOneBy(['template_active' => true]);

            // get this user devices (if any)
            $user_devices = $this->em->getRepository(Devices::class)->findOneBy(['devices_user' => $user_array]);

            if($user_devices != null){

                $this->devices_array = [
                    'name'             => $user_devices->getName(),
                    'model'            => $user_devices->getModel(),
                    'revision'         => $user_devices->getRevision(),
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
                ];
            }

            return $this->render('home_page/index.html.twig', [
                'is_mobile'             => $this->tools->isMobile(),
                'site_settings' => [
                    'site_general'  => $serializer->decode($site_general_content, 'json'),
                    'site_style'    => $serializer->decode($site_style_content, 'json'),
                ],

                'user_settings' => [
                    'user_ip'    => $user_settings->getUserIp(),
                    'user_ipv6'  => $user_settings->getIpv6(),
                    'template' => [
                        'id'        => $templates_settings->getId(),
                        'active'    => $templates_settings->isTemplateActive(),
                        'name'      => $templates_settings->getTemplateName(),
                        'user'      => $user_settings->getUser(),
                    ],

                    'profile'    => $serializer->decode($site_profile_content, 'json'),
                    'network'    => $serializer->decode($site_network_content, 'json'),
                ],
                'devices' => $this->devices_array,
            ]);
        }

        $this->denyAccessUnlessGranted('PUBLIC_ACCESS');

        return $this->render('home_page/index.html.twig', [
            'is_mobile'             => $this->tools->isMobile(),
        ]);
    }
}
