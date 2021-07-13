<?php


namespace App\Controller;


use App\Form\TaskType;
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
        $form = $this->createForm(TaskType::class);
        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
                ]
        );
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function edit()
    {
        $form = $this->createForm(TaskType::class);
        return $this->render('task/edit.html.twig', [
            'form' => $form->createView()
        ]);
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
