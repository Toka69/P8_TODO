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

    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function create()
    {

    }

    /**
     * @Route("/users/edit", name="user_edit")
     */
    public function edit()
    {

    }
}