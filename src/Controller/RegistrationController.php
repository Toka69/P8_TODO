<?php

namespace App\Controller;

use App\Entity\User;
use App\Handler\RegistrationHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class RegistrationController
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        Security $security,
        HandlerFactoryInterface $handlerFactory
    ): Response {
        if ($security->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->redirectToRoute("homepage");
        }

        $user = new User();

        $handler = $handlerFactory->createHandler(RegistrationHandler::class);

        if ($handler->handle($request, $user)) {
            return $this->redirectToRoute("security_login");
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $handler->createView(),
        ]);
    }
}
