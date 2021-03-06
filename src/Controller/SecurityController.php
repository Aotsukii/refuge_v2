<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository, UrlGeneratorInterface $urlGenerator): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // check if the user is logged in and redirect according to roles
        $currentUser = $this->getUser();
        if ($currentUser != NULL) {
            $roles = $currentUser->getRoles();
        
            if (in_array("ROLE_ADMIN", $roles)) {
                return new RedirectResponse($urlGenerator->generate('admin'));
            }

            if (in_array("ROLE_FA", $roles)) {
                return new RedirectResponse($urlGenerator->generate('FA'));
            }

            if (in_array("ROLE_MEMBER", $roles)) {
                return new RedirectResponse($urlGenerator->generate('member'));
            }

            if (in_array("ROLE_SUPER_ADMIN", $roles)) {
                return new RedirectResponse($this->urlGenerator->generate('backend'));
            }
        }
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    /**
     * @Route("/2fa", name="2fa_login")
     */
    public function check2fa(TokenStorageInterface $tokenStorageInterface, GoogleAuthenticatorInterface $googleAuthenticatorInterface)
    {
        $qrCode = $googleAuthenticatorInterface->getQRContent($this->getUser());
        $url = "http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=".$qrCode;
        return $this->render(
            'security/2fa_login.html.twig', [
                'url' => $url
            ]
        );
    }
}
