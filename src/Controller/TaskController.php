<?php

namespace App\Controller;

use App\Entity\Task;
use App\Handler\CreateTaskHandler;
use App\Handler\EditTaskHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package App\Controller
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function list(TaskRepository $taskRepository, AdapterInterface $cache): Response
    {
        $tasksNotDone = $cache->getItem('tasksNotDone');
        if (!$tasksNotDone->isHit()) {
            $tasksNotDone->set($taskRepository->findBy(['user' => $this->getUser(), 'isDone' => false]));
            $cache->save($tasksNotDone);
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $cache->getItem('tasksNotDone')->get()
        ]);
    }

    /**
     * @Route("/tasks/done", name="task_done")
     */
    public function listIsDone(TaskRepository $taskRepository, AdapterInterface $cache): Response
    {
        $tasksIsDone = $cache->getItem('tasksIsDone');
        if (!$tasksIsDone->isHit()) {
            $tasksIsDone->set($taskRepository->findBy(['user' => $this->getUser(), 'isDone' => true]));
            $cache->save($tasksIsDone);
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $cache->getItem('tasksIsDone')->get()
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function create(Request $request, HandlerFactoryInterface $handlerFactory)
    {
        $task = new Task();

        $handler = $handlerFactory->createHandler(CreateTaskHandler::class);

        if ($handler->handle($request, $task)) {
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $handler->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function edit(Task $task, Request $request, HandlerFactoryInterface $handlerFactory)
    {
        $this->denyAccessUnlessGranted(
            'EDIT',
            $task,
            "You are not the owner of this task and you are not authorized to edit it."
        );

        $handler = $handlerFactory->createHandler(EditTaskHandler::class);

        if ($handler->handle($request, $task)) {
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $handler->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggle(Task $task, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted(
            'TOGGLE',
            $task,
            "You are not the owner of this task and you are not authorized to toggle it."
        );

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
        $this->denyAccessUnlessGranted(
            'DELETE',
            $task,
            "You are not the owner of this task and you are not authorized to delete it."
        );

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
