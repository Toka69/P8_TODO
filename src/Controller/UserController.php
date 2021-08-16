<?php

namespace App\Controller;

use App\Entity\User;
use App\Handler\CreateUserHandler;
use App\Handler\EditUserHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page!")
     */
    public function list(UserRepository $userRepository, AdapterInterface $cache): Response
    {
        $users = $cache->getItem('users');
        if (!$users->isHit()) {
            $users->set($userRepository->findAll());
            $cache->save($users);
        }
        return $this->render('user/list.html.twig', ['users' => $cache->getItem('users')->get()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     * @IsGranted("ROLE_ADMIN", message="Vous n'êtes pas autorisé à accéder à cette page!")
     */
    public function create(
        Request $request,
        HandlerFactoryInterface $handlerFactory
    ): Response {
        $user = new User();

        $handler = $handlerFactory->createHandler(CreateUserHandler::class);

        if ($handler->handle($request, $user)) {
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'formView' => $handler->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function edit(
        User $user,
        Security $security,
        HandlerFactoryInterface $handlerFactory,
        Request $request
    ) {
        $this->denyAccessUnlessGranted(
            'EDIT',
            $user,
            "Vous n'êtes pas cet utilisateur et vous n'êtes pas autorisé à l'éditer!"
        );

        $handler = $handlerFactory->createHandler(EditUserHandler::class);

        if ($handler->handle($request, $user)) {
            if (current($security->getUser()->getRoles()) === "ROLE_ADMIN") {
                return $this->redirectToRoute('user_list');
            }

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/edit.html.twig', ['form' => $handler->createView(), 'user' => $user]);
    }
}
