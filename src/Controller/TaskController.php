<?php

namespace App\Controller;

use App\Entity\Task;
use App\Handler\CreateTaskHandler;
use App\Handler\EditTaskHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
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
    protected CacheItem $cacheUsers;

    protected CacheItem $cacheTasksIsDone;

    protected CacheItem $cacheTasksNotDone;

    public function __construct(AdapterInterface $cache, UserRepository $userRepository, TaskRepository $taskRepository)
    {
        $tasksNotDone = $cache->getItem('tasksNotDone');
        if (!$tasksNotDone->isHit()) {
            $tasksNotDone->set($taskRepository->findBy(['isDone' => false]));
            $cache->save($tasksNotDone);
        }
        $this->cacheTasksNotDone = $tasksNotDone;

        $tasksIsDone = $cache->getItem('tasksIsDone');
        if (!$tasksIsDone->isHit()) {
            $tasksIsDone->set($taskRepository->findBy(['isDone' => true]));
            $cache->save($tasksIsDone);
        }
        $this->cacheTasksIsDone = $tasksIsDone;

        $users = $cache->getItem('users');
        if (!$users->isHit()) {
            $users->set($userRepository->findAll());
            $cache->save($users);
        }
        $this->cacheUsers = $users;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function list(): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->cacheTasksNotDone->get(),
            'users' => $this->cacheUsers->get()
        ]);
    }

    /**
     * @Route("/tasks/done", name="task_done")
     */
    public function listIsDone(): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->cacheTasksIsDone->get(),
            'users' => $this->cacheUsers->get()
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
            "Vous n'??tes pas le propri??taire de cette t??che et vous n'??tes pas autoris?? ?? la modifier!"
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
            "Vous n'??tes pas le propri??taire de cette t??che et vous n'??tes pas autoris?? ?? la basculer!"
        );

        if ($task->getIsDone() === false) {
            $message = 'La t??che %s a bien ??t?? marqu??e comme faite.';
        } else {
            $message = 'La t??che %s a bien ??t?? marqu??e comme non faite.';
        }

        $task->setIsDone(!$task->getIsDone());
        $entityManager->flush();
        $this->addFlash('success', sprintf($message, $task->getTitle()));

        $referer = $request->headers->get('referer');

        if ($referer !== null && empty($referer) === false) {
            return new RedirectResponse($referer);
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function delete(Task $task, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted(
            'DELETE',
            $task,
            "Vous n'??tes pas le propri??taire de cette t??che et vous n'??tes pas autoris?? ?? l'effacer!"
        );

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La t??che a bien ??t?? supprim??e.');

        $referer = $request->headers->get('referer');

        if ($referer !== null && empty($referer) === false) {
            return new RedirectResponse($referer);
        }

        return $this->redirectToRoute('homepage');
    }
}
