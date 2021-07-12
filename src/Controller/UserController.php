<?php


namespace App\Controller;


use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users/list", name="user_list")
     */
    public function list()
    {
        return $this->render('user/list.html.twig');
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function create()
    {
        $form = $this->createForm(UserType::class);

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