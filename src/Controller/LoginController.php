<?php

namespace App\Controller;
use App\Helpers\Tools;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LoginController extends AbstractController
{
    private ?string $lastUsername = null;
    protected EntityManagerInterface $entityManager;
    private ?Tools $tools;

    #[Route('/login', name: 'app_user_login')]
    #[Pure] public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager   = $entityManager;
        $this->tools                 = new Tools();
    }

    #[\Symfony\Component\Routing\Annotation\Route(path: '/login', name: 'app_user_login')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_user_login', ['_locale' => 'en']);
    }

    #[\Symfony\Component\Routing\Annotation\Route(path: '/{_locale<%app.supported_locales%>}/users/login', name: 'app_user_login', methods: ["POST", "GET"])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_page'); // Replace 'app_home_page' with your target route name
        }

        $this->denyAccessUnlessGranted('PUBLIC_ACCESS');

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'is_mobile'     => $this->tools ->isMobile(),
            'style'         => ['alerts' => ['type' => 'primary'], 'form' => ['width' => '12']],
            'error'         => $error,
            'last_username' => $lastUsername,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->redirect($this->generateUrl('app_home_page'));
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/reset_password', name: 'reset_password')]
    public function reset(): void {

    }
}
