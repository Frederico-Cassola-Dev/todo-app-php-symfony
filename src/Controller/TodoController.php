<?php
// src/Controller/TodoController.php
namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'app_todo')]
    public function index(TaskRepository $repository): Response
    {
        $tasks = $repository->findAll();

        return $this->render('todo/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/todo/new', name: 'app_todo_new')]
    public function new(EntityManagerInterface $em): Response
    {
        $task = new Task();
        $task->setTitle('Nouvelle tâche exemple');
        $task->setDescription('Description par défaut');
        $task->setIsDone(false);
        $em->persist($task);
        $em->flush();

        return $this->redirectToRoute('app_todo');
    }

    #[Route('/todo/{id}/toggle', name: 'app_todo_toggle')]
    public function toggle(Task $task, EntityManagerInterface $em): Response
    {
        $task->setIsDone(!$task->isDone());
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
}

