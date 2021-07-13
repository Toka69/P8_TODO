<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function create(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur a été créé');

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function edit()
    {
        $form = $this->createForm(UserType::class);

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}