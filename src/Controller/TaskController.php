<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function list()
    {

    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function create()
    {

    }

    /**
     * @Route("/tasks/edit", name="task_edit")
     */
    public function edit()
    {

    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggle()
    {

    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function delete()
    {

    }
}
