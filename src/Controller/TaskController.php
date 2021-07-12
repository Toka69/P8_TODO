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
        return $this->render('task/list.html.twig');
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function create()
    {
        return $this->render('task/create.html.twig');
    }

    /**
     * @Route("/tasks/edit", name="task_edit")
     */
    public function edit()
    {
        return $this->render('task/edit.html.twig');
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
