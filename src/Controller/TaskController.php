<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function list(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['user' => $this->getUser(), 'isDone' => false])]);
    }

    /**
     * @Route("/tasks/done", name="task_done")
     */
    public function listIsDone(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['user' => $this->getUser(), 'isDone' => true])]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setIsDone(false)
                ->setUser($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function edit(Task $task, Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('EDIT', $task, "You are not the owner of this task and you are not authorized to edit it.");

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggle(Task $task, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('TOGGLE', $task, "You are not the owner of this task and you are not authorized to toggle it.");

        if ($task->getIsDone() === false) {
            $message = 'La tâche %s a bien été marquée comme faite.';
        } else {
            $message = 'La tâche %s a bien été marquée comme non faite.';
        }

        $task->setIsDone(!$task->getIsDone());
        $entityManager->flush();
        $this->addFlash('success', sprintf($message, $task->getTitle()));

        $referer = $request->headers->get('referer');

        if ($referer !== null) {
            return new RedirectResponse($referer);
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function delete(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('DELETE', $task, "You are not the owner of this task and you are not authorized to delete it.");

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
