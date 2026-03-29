<?php

// src/Controller/TodoController.php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TodoController extends AbstractController
{
    #[Route('/todo/create', name: 'app_todo_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Tâche créée !');

            return $this->redirectToRoute('app_todo');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Tâche error !');
        }

        return $this->render('todo/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/todo', name: 'app_todo')]
    public function index(TaskRepository $repository, Request $request): Response
    {
        $sort = $request->query->get('sort', 'updatedAt');
        $dir = $request->query->get('direction', 'DESC');

        $tasks = $repository->findAllSorted($sort, $dir);

        var_dump($sort, $dir);

        return $this->render('todo/index.html.twig', [
            'tasks' => $tasks,
            'activeSort' => $sort,
            'direction' => $dir,
        ]);
    }

    #[Route('/todo/new', name: 'app_todo_new')]
    public function new(EntityManagerInterface $em): Response
    {
        $task = new Task();
        $task->setTitle('Nouvelle tâche exemple');
        $task->setDescription('Description par défaut');
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setUpdatedAt(new \DateTimeImmutable());
        $task->setIsDone(false);
        $em->persist($task);
        $em->flush();

        return $this->redirectToRoute('app_todo');
    }

    #[Route('/todo/{id}/toggle', name: 'app_todo_toggle')]
    public function toggle(Task $task, EntityManagerInterface $em): Response
    {
        $task->setIsDone(!$task->isDone());
        $task->setUpdatedAt(new \DateTimeImmutable());
        $em->flush();

        return $this->redirectToRoute('app_todo');
    }

    #[Route('/todo/{id}/delete', name: 'app_todo_delete')]
    public function delete(Task $task, EntityManagerInterface $em): Response
    {
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('app_todo');
    }

    #[Route('/todo/{id}/edit', name: 'app_todo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt(new \DateTimeImmutable());

            $em->flush();
            $this->addFlash('success', 'Taché modifiée !');

            return $this->redirectToRoute('app_todo');
        }

        return $this->render('todo/edit.html.twig', ['form' => $form->createView(), 'task' => $task]);
    }
}
