<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\TodosRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    private $taskRepo;
    private $todoRepo;

    public function __construct(TaskRepository $repo, TodosRepository $todoRepo)
    {
        $this->taskRepo = $repo;
        $this->todoRepo = $todoRepo;

    }

    function getHeaderOrQueryData($request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            return is_array($data) ? $data : array();
        } else {
            return array(
                "name" => $request->query->get('name'),
                "checked" => $request->query->get('checked')
            );
        }
    }

    /**
     * @Route("/api/lists/{idList}/items/{idTask}", methods={"GET"}, requirements={"id"="\d+", "idTask"="\d+"})
     */
    public function getOneTask($idList, $idTask): JsonResponse
    {
        $task = $this->taskRepo->findOneByUser($idList, $idTask, $this->getUser());
        if ($task == null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        return new JsonResponse([
            'item' => $task,
        ]);
    }

    /**
     * @Route("/api/lists/{id}/items", methods={"POST"})
     */
    public function createTask($id, Request $request, ObjectManager $manager): JsonResponse
    {
        $data = $this->getHeaderOrQueryData($request);
        if ($data["name"] == null OR $data["checked"] == null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }

        // GET Current List
        $todo = $this->todoRepo->findOneByUser($id, $this->getUser());
        if ($todo == null) {
            return new JsonResponse([
                'success' => false,
                'todo' => "Not Found",
            ], 400);
        }

        $task = new Task();
        $task->setName($data["name"])
            ->setChecked($data["checked"])
            ->setTodos($todo);

        $manager->persist($task);
        $manager->flush();

        return new JsonResponse([
            "success" => true,
            'item' => $task,
        ], 200);
    }

    /**
     * @Route("/api/lists/{idList}/items/{idTask}", methods={"PUT"})
     */
    public function checkTask(ObjectManager $manager, Request $request, $idList, $idTask)
    {
        $data = $this->getHeaderOrQueryData($request);
        if ($data["checked"] == null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        $task = $this->taskRepo->findOneByUser($idList, $idTask, $this->getUser());
        if ($task == null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        $task->setChecked($data["checked"]);
        $manager->flush();

        return new JsonResponse([
            'success' => true,
            'task' => $task,
        ]);
    }

    /**
     * @Route("/api/lists/{idList}/items/{idTask}", methods={"DELETE"})
     */
    public function deleteTask(ObjectManager $manager, $idList, $idTask)
    {
        $task = $this->taskRepo->findOneByUser($idList, $idTask, $this->getUser());
        if ($task == null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }

        $manager->remove($task);
        $manager->flush();

        return new JsonResponse([
            'success' => true,
            'delete' => $task->getName()
        ], 200);
    }
}