<?php

namespace App\Controller;

use App\Entity\SiteSettings;
use App\Entity\Templates;
use App\Entity\Users;
use App\Entity\UserSettings;
use App\Entity\Visitors;
use App\Form\RegisterType;
use App\Security\LoginCustomAuthenticator;
use App\Services\UsersService;
use App\Helpers\Tools;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use GeoIp2\Exception\AddressNotFoundException;
use JetBrains\PhpStorm\Pure;
use MaxMind\Db\InvalidDatabaseException;
use GeoIp2\Database\Reader;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Uid\UuidV1;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class RegisterController extends AbstractController
{

    private Tools $tools;
    private EntityManagerInterface $em;
    private UsersService $userService;
    protected ?string $site_name;
    protected ?string $site_slogan;
    private ?string $template_name;
    private string $ip;
    private string $ipv6;
    private string $public_ip;
    protected ?array $user_settings_array;
    private $visitor_array;

    #[Pure] public function __construct( UsersService $us, RouterInterface $r, EntityManagerInterface $entityManager, ManagerRegistry $do){

        $this->em                  = $entityManager;
        $this->tools               = new Tools;
        $this->site_name           = 'Change Site Name';
        $this->site_slogan         = 'Change Site Slogan';
        $this->template_name       = 'default';
        $this->ip                  = '';
        $this->ipv6                = '';
        $this->public_ip           = '';
        $this->user_settings_array = [];
        $this->userService         = $us;
        //$this->visitors            = null;
    }


    #[Route('/register', name: 'app_user_register')]
    public function registerNoLocal(): Response
    {

        return $this->redirectToRoute('app_user_register', ['_locale' => 'en']);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidDatabaseException
     * @throws TransportExceptionInterface
     * @throws InvalidDatabaseException
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException|AddressNotFoundException|ExceptionInterface
     */
    #[Route('/{_locale<%app.supported_locales%>}/users/register', name: 'app_user_register', methods: ["GET", "POST"])]
    public function users_register(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, UserAuthenticatorInterface $userAuthenticator, LoginCustomAuthenticator $authenticator ): Response {

        $this->denyAccessUnlessGranted('PUBLIC_ACCESS');

        $this->ip        = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $this->ipv6      = $this->tools->getIpv6();
        $this->public_ip = $this->tools->getIpv4();

        $GeoLiteDatabasePath = $this->getParameter('kernel.project_dir').'/src/GeolitCity/GeoLite2-City.mmdb';

        $reader = new Reader($GeoLiteDatabasePath);

        try {
            // if you are in the production environment you can retrieve the
            // user's IP with $request->getClientIp()
            // Note that in a development environment 127.0.0.1 will
            // throw the AddressNotFoundException

            $record = $reader->city($this->ipv6);

        }
        catch (AddressNotFoundException $ex) {
            // Couldn't retrieve geo information from the given IP
            return new Response("It wasn't possible to retrieve information about the provided IP");
        }

        $encoders    = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer  = new Serializer($normalizers, $encoders);

        if ($this->getUser()) {

            return $this->redirectToRoute('all_app_home');
        }

        $user          = new Users();        // init new User entity

        // 1) build the form
        $user_form = $this->createForm(RegisterType::class, $user);

        // 2) handle submit (will only happen on POST)
        $user_form->handleRequest($request);

        if ($user_form->isSubmitted()) {

            if ($user_form->isValid()) {

                $user = $user_form->getData();

                // 3) Encode the password (you could also do this via Doctrine listener)
                $em = $doctrine->getManager();

                $user->setUuid(new UuidV1());

                $plaintextPassword = $user->getPassword();

                $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);

                $user->setPassword($hashedPassword);

                $user->setRoles(['ROLE_USER']);

                $user->setRegisterDate(); /* this will set the register date to the today's date */

                $last_update = $user->getRegisterDate();

                $user->setLastUpdate($last_update); // while register set lat update to register date

                $accepted = $user_form->get('termsAccepted')->getData();

                $user->setTermsAccepted($accepted);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Welcome '.$user->getUserEmail());

                /* log in user after registration */
                //$this->authenticateUser($request, $user);

                $userAuthenticator->authenticateUser($user, $authenticator, $request);

                // query database to get user info
                $user_array = $this->userService->getAccUser(['username' => $this->getUser()->getUserIdentifier()]);

                $site_settings = new SiteSettings(); // init new SiteSettings entity

                $site_general_settings  = [

                    "general" =>  [

                        "site_name"        => DEFAULT_SITE_NAME,
                        "slogan"           => DEFAULT_SITE_SLOGAN,
                        "show_navbar"      => 'YES',
                        "show_brand_div"   => 'YES',
                        "show_slogan_div"  => 'YES',
                        "brand_text"       => 'YES',
                        "brand_image"      => 'YES',
                        "brand_image_path" => "/images/templates/logo.png",
                    ],
                ];

                $site_general_content  = $serializer->serialize($site_general_settings, 'json');

                $site_general_data     = json_decode($site_general_content, true);

                $site_settings->setUser($user);
                $site_settings->setSettingsName(USERS_STRING);
                $site_settings->setSiteName(DEFAULT_SITE_NAME);
                $site_settings->setSiteSetting($site_general_data);

                $user_settings = new UserSettings(); // init new UserSettings entity

                $user_style_settings = [
                    'style' => [
                        'template_name'    => DEFAULT_SITE_TEMPLATE_NAME,
                        'text_color'       => '#000000',
                        'background_color' => '#ffffff',
                    ],
                ];

                $user_network_settings = [
                    'network' => [
                        'ip'   => $this->public_ip,
                        'ipv6' => $this->ipv6,
                    ],
                    'position' => [
                        'lat'         => $record->location->latitude,
                        'lng'         => $record->location->longitude,
                        'country'     => $record->country->name,
                        'city'        => $record->city->name,
                        'postal_code' => $record->postal->code

                    ],
                ];

                $user_profile_settings = [
                    'show_modules' => false,
                    'show_navbar' => true,
                ];

                $user_style_settings_content   = $serializer->serialize($user_style_settings, 'json');
                $user_network_settings_content = $serializer->serialize($user_network_settings, 'json');
                $user_profile_settings_content = $serializer->serialize($user_profile_settings, 'json');

                $user_style_settings_data      = json_decode($user_style_settings_content, true);
                $user_network_settings_data    = json_decode($user_network_settings_content, true);
                $user_profile_settings_data    = json_decode($user_profile_settings_content, true);

                $user_settings->setUser($user);
                $user_settings->setStyleName(DEFAULT_SITE_TEMPLATE_NAME);
                $user_settings->setUserIp($this->public_ip);
                $user_settings->setIpv6($this->ipv6);
                $user_settings->setSiteStyle($user_style_settings_data);
                $user_settings->setNetworkSettings($user_network_settings_data);
                $user_settings->setProfileSettings($user_profile_settings_data);

                $templates = new Templates();

                $templates->setUser($user);
                $templates->setTemplateName(DEFAULT_SITE_TEMPLATE_NAME);
                $templates->setTemplateActive(true);
                $templates->setSiteName(DEFAULT_SITE_NAME);
                $templates->setSiteSlogan(DEFAULT_SITE_SLOGAN);

                $visitors = new Visitors();

                $visitors->setIp($this->public_ip);
                $visitors->setIpv6($this->ipv6);
                $visitors->setCountry($record->country->name);
                $visitors->setCity($record->city->name);
                $visitors->setCodePostal($record->postal->code);
                $visitors->setLat($record->location->latitude);
                $visitors->setLng($record->location->longitude);
                $visitors->setUserAgent($this->tools->userAgent());
                $visitors->setVisitDate();
                $visitors->setIsMobile($this->tools->isMobile());

                $em->persist($visitors);
                $em->persist($user_settings);
                $em->persist($site_settings);
                $em->persist($templates);
                $em->flush();

                $site_settings         = $this->em->getRepository(SiteSettings::class)->findOneBy(['site_settings_user' => $user_array]);

                $site_general_content  = $serializer->serialize($site_settings->getSiteSetting(), 'json');

                $user_settings         = $this->em->getRepository(UserSettings::class)->findOneBy(['user' => $user_array]);

                $site_style_content    = $serializer->serialize($user_settings->getSiteStyle(), 'json');
                $site_profile_content  = $serializer->serialize($user_settings->getProfileSettings(), 'json');
                $site_network_content  = $serializer->serialize($user_settings->getNetworkSettings(), 'json');

                $templates_settings = $this->em->getRepository(Templates::class)->findOneBy(['template_name' => $this->template_name]);


                return $this->redirectToRoute('app_home_page');

                /*return $this->redirectToRoute('app_home_page', [
                    'site_settings' => [
                        'site_general'  => $serializer->decode($site_general_content, 'json'),
                    ],

                    'user_settings' => [
                        'user_ip'    => $user_settings->getUserIp(),
                        'user_ipv6'  => $user_settings->getIpv6(),
                        'site_style' => $serializer->decode($site_style_content, 'json'),
                        'profile'    => $serializer->decode($site_profile_content, 'json'),
                        'network'    => $serializer->decode($site_network_content, 'json'),
                    ],

                    'templates' => [
                        'template_name' => $templates_settings,
                    ],
                ]);*/
            }
            else
            {

            }
        }

        return $this->render('register/index.html.twig', [
            'user_form'          => $user_form,
            'form_title'         => $translator->trans('application.text.new.user.register.form.label'), //'Users Registration Form',
            'style' => ['alerts' => ['type' => 'primary'], 'form' => ['width' => '8']],
            'page_title'         => 'User Register',
            'user_level'         => 'USER',
            'is_mobile'          => $this->tools->isMobile(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function authenticateUser(Request $request, Users $user): void
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $request->getSession()->set('_security_main', serialize($token));
    }
}
