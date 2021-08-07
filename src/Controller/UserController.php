<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Handler\UserCreateHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     * @IsGranted("ROLE_ADMIN")
     */
    public function list(UserRepository $userRepository, AdapterInterface $cache): Response
    {
        $users = $cache->getItem('users');
        if(!$users->isHit())
        {
            $users->set($userRepository->findAll());
            $cache->save($users);
        }
        return $this->render('user/list.html.twig', ['users' => $cache->getItem('users')->get()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(
        EntityManagerInterface $em,
        Request $request,
        HandlerFactoryInterface $handlerFactory

    ): Response {
        $user = new User();

        $handler = $handlerFactory->createHandler(UserCreateHandler::class);

        if($handler->handle($request, $user)) {
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
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->denyAccessUnlessGranted(
            'EDIT',
            $user,
            "You are not this user and you are not authorized to edit it."
        );

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            if ($user->getRoles() === ["ROLE_ADMIN"]) {
                return $this->redirectToRoute('user_list');
            }
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
