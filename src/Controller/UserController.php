<?php


namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users/list", name="user_list")
     * @IsGranted("ROLE_USER")
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
        return $this->render('user/create.html.twig');
    }

    /**
     * @Route("/users/edit", name="user_edit")
     */
    public function edit()
    {
        return $this->render('user/edit.html.twig');
    }
}