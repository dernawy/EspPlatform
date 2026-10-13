<?php

namespace App\Controller;

use App\Entity\SiteSettings;
use App\Entity\Templates;
use App\Entity\Users;
use App\Entity\UserSettings;
use App\Entity\UserStyles;
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
    protected EntityManagerInterface $em;
    private UsersService $userService;
    protected ?string $site_name;
    protected ?string $site_slogan;
    protected ?string $template_name;
    protected string $ip;
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

            return $this->redirectToRoute('app_home_page');
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

                //$this->addFlash('success', 'Welcome '.$user->getUserEmail());

                $userAuthenticator->authenticateUser($user, $authenticator, $request);

                // set user template (default when register)
                $templates = new Templates();

                $templates->setTemplateActive(true);
                $templates->setUser($user);
                $templates->setTemplateName(DEFAULT_SITE_TEMPLATE_NAME);

                $user_style = new UserStyles();

                $user_style->setUser($user);
                $user_style->setTemplateName(DEFAULT_SITE_TEMPLATE_NAME);
                $user_style->setStyleName('default');

                $user_style_settings = [
                    'style' => [
                        'template_name'    => DEFAULT_SITE_TEMPLATE_NAME,
                        'pages' => [
                            'home'          => [
                                'background_color' => '#1a1d20',
                                'title' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '1.5',
                                    'weight' => 'normal',
                                ],
                                'text' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '0.5',
                                    'weight' => 'normal',
                                ],

                                'buttons' => [
                                    'text' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo, sans-serif',
                                        ],
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'color'  => '#ffffff',
                                        'font_size'   => '1',
                                        'weight' => 'bold',
                                    ],
                                    'width' => 'auto',
                                    'style' => 'btn btn-outline-primary',
                                ]
                            ],
                            'profile'       => [
                                'background_color' => '#1a1d20',
                                'title' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '1.5',
                                    'weight' => 'bold',
                                ],
                                'text' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '0.5',
                                    'weight' => 'normal',
                                ],

                                'buttons' => [
                                    'text' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo, sans-serif',
                                        ],
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'color'  => '#ffffff',
                                        'font_size'   => '1',
                                        'weight' => 'bold',
                                    ],
                                    'width' => 'auto',
                                    'style' => 'btn btn-outline-primary',
                                ]
                            ],
                            'user_settings' => [
                                'background_color' => '#1a1d20',
                                'title' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '1.5',
                                    'weight' => 'bold',
                                ],
                                'text' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '0.5',
                                    'weight' => 'normal',
                                ],
                                'tabs' => [
                                    'header' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo, sans-serif',
                                        ],
                                        'background_color' => '#7285b1',
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'active_color' => '#ffffff',
                                        'color' => '#ffffff',
                                        'font_size' => '1',
                                        'style' => 'nav-tabs',
                                    ],
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'background_color' => '#b2c2d6',
                                    'hover_color' => '#141471',
                                    'color' => 'rgba(0,0,0,0.55)',
                                    'font_size' => '1',
                                    'style' => 'nav-tabs',
                                ],
                                'buttons' => [
                                    'text' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo, sans-serif',
                                        ],
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'color'  => '#ffffff',
                                        'font_size'   => '1',
                                        'weight' => 'bold',
                                    ],
                                    'width' => 'auto',
                                    'style' => 'btn btn-outline-primary',
                                ]
                            ],
                            'site_settings' => [
                                'background_color' => '#1a1d20',
                                'title' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '1.5',
                                    'weight' => 'bold',
                                ],
                                'text' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '0.5',
                                    'weight' => 'normal',
                                ],

                                'buttons' => [
                                    'text' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo, sans-serif',
                                        ],
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'color'  => '#ffffff',
                                        'font_size'   => '1',
                                        'weight' => 'bold',
                                    ],
                                    'width' => 'auto',
                                    'style' => 'btn btn-outline-primary',
                                ]
                            ],
                            'devices'       => [
                                'background_color' => '#1a1d20',
                                'title' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '1.5',
                                    'weight' => 'bold',
                                ],
                                'text' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '0.5',
                                    'weight' => 'normal',
                                ],

                                'buttons' => [
                                    'text' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo, sans-serif',
                                        ],
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'color'  => '#ffffff',
                                        'font_size'   => '1',
                                        'weight' => 'bold',
                                    ],
                                    'width' => 'auto',
                                    'style' => 'btn btn-outline-primary',
                                ]
                            ],
                            'status'        => [
                                'background_color' => '#1a1d20',
                                'title' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '1.5',
                                    'weight' => 'bold',
                                ],
                                'text' => [
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'color'  => '#ffffff',
                                    'font_size'   => '0.5',
                                    'weight' => 'normal',
                                ],
                                'tabs' => [
                                    'header' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo, sans-serif',
                                        ],
                                        'background-color' => '#7285b1',
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'active_color' => '#ffffff',
                                        'color' => 'rgba(0,0,0,0.55)',
                                        'font_size' => '1',
                                        'style' => 'nav-tabs',
                                    ],
                                    'ar' =>[
                                        'font_family'   => 'Cairo, sans-serif',
                                    ],
                                    'font_family'   => 'Bebas Neue, sans-serif',
                                    'background_color' => '#b2c2d6',
                                    'hover_color' => '#141471',
                                    'color' => 'rgba(0,0,0,0.55)',
                                    'font_size' => '1',
                                    'style' => 'nav-tabs',
                                ],
                                'buttons' => [
                                    'text' => [
                                        'ar' =>[
                                            'font_family'   => 'Cairo,sans-serif',
                                        ],
                                        'font_family'   => 'Bebas Neue, sans-serif',
                                        'color'  => '#ffffff',
                                        'font_size'   => '1',
                                        'weight' => 'bold',
                                    ],
                                    'width' => 'auto',
                                    'style' => 'btn btn-outline-primary',
                                ]
                            ],
                        ],
                    ],
                ];

                // serialize user general settings
                $user_style_content  = $serializer->serialize($user_style_settings, 'json');

                // encode user general settings
                $user_style_data     = json_decode($user_style_content, true);

                $user_style->setStyleData($user_style_data);

                // init user general settings
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

                // serialize user general settings
                $site_general_content  = $serializer->serialize($site_general_settings, 'json');

                // encode user general settings
                $site_general_data     = json_decode($site_general_content, true);

                // attach user general settings to db table
                $site_settings->setTemplate($templates);
                $site_settings->setUser($user);
                $site_settings->setSettingsName(USERS_STRING);
                $site_settings->setSiteName(DEFAULT_SITE_NAME);
                $site_settings->setSiteSetting($site_general_data);

                // init user settings
                $user_settings = new UserSettings();

                $user_network_settings = [
                    'network' => [
                        'ip'   => $this->public_ip,
                        'ipv6' => $this->ipv6,
                    ],
                    'position' => [
                        'latitude'    => $record->location->latitude,
                        'longitude'   => $record->location->longitude,
                        'country'     => $record->country->name,
                        'city'        => $record->city->name,
                        'postal_code' => $record->postal->code

                    ],
                ];

                $user_profile_settings = [
                    'show_modules' => false,
                    'show_navbar' => true,
                ];

                $user_network_settings_content = $serializer->serialize($user_network_settings, 'json');
                $user_profile_settings_content = $serializer->serialize($user_profile_settings, 'json');

                $user_network_settings_data    = json_decode($user_network_settings_content, true);
                $user_profile_settings_data    = json_decode($user_profile_settings_content, true);

                $user_settings->setUser($user);
                $user_settings->setUserIp($this->public_ip);
                $user_settings->setIpv6($this->ipv6);
                $user_settings->setNetworkSettings($user_network_settings_data);
                $user_settings->setProfileSettings($user_profile_settings_data);
                $user_settings->setUserStyle($user_style);

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

                return $this->redirectToRoute('app_home_page');
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
