<?php

namespace App\Controller;
date_default_timezone_set('Europe/Bucharest');
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Form\TaskType;

class ToDoController extends AbstractController
{
    
    #[Route('/list', name: 'app_list')]
    public function list(Request $request, TaskRepository $taskRepository, UrlGeneratorInterface $urlGenerator): Response
    {
        $page = $request->query->getInt('page', 1); 
        $itemsPerPage = 10; 
        $tasks = $taskRepository->findAll();

        $totalPages = ceil(count($tasks) / $itemsPerPage);
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $startIndex = ($page - 1) * $itemsPerPage;
        $endIndex = $startIndex + $itemsPerPage;

        $tasksOnPage = array_slice($tasks, $startIndex, $itemsPerPage);

        return $this->render('to_do/list.html.twig', [
            'tasks' => $tasksOnPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'urlGenerator' => $urlGenerator,
        ]);
    }

    #[Route('/view/{id}', name: 'app_view')]
    public function view(int $id, CategoryRepository $taskRepository): Response
    {
        $task = $taskRepository->find($id);
        if ($task === null) {
            throw $this->createNotFoundException('Task not found');
        }
        return $this->render('to_do/view.html.twig', ['task' => $task]);
    }


    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {

            $entityManager->remove($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_list');
        }

        return $this->render('to_do/delete.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/create', name: 'app_create')]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_list');
        }

        return $this->render('to_do/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/update/{id}', name: 'app_update')]
    public function update(Request $request, Task $task, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_list');
        }

        return $this->render('to_do/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
