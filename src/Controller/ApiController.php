<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    private $taskRepo;

    public function __construct(TaskRepository $repo)
    {
        $this->taskRepo = $repo;
    }

    function getHeaderOrQueryData($request){
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            echo "test";
            return json_decode($request->getContent(), true);
        }
        else{
            return array(
                "name" => $request->query->get('name'),
                "checked" => $request->query->get('checked')
            );
        }
    }

    /**
     * @Route("/api/task", name="api_get_tasks",  methods={"GET"})
     */
    public function getAllTask()
    {
        $tasks = $this->taskRepo->findAll();

        return new JsonResponse([
            'tasks' => $tasks
        ], 200);
    }

    /**
     * @Route("/api/task/{id}", name="api_get_task", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getOneTask($id): JsonResponse
    {
        $task = $this->taskRepo->find($id);
        if($task == null ){
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        return new JsonResponse([
            'task' => $task,
        ]);
    }

    /**
     * @Route("/api/task", name="api_create_task", methods={"POST"})
     */
    public function createTask(Request $request, ObjectManager $manager): JsonResponse
    {
        $data = $this->getHeaderOrQueryData($request);
        if($data["name"] == null){
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        $task = new Task();
        $task->setName($data["name"])
            ->setChecked($data["checked"]);

        $manager->persist($task);
        $manager->flush();

        return new JsonResponse([
            'task' => $task,
        ], 200);
    }

    /**
     * @Route("/api/task/{id}", name="api_task_update", methods={"PUT"})
     */
    public function updateTask(ObjectManager $manager, Request $request, $id)
    {
        $data = $this->getHeaderOrQueryData($request);
        if($data["name"] == null){
             return new JsonResponse([
                 'success' => false,
             ], 400);
        }
        $task = $this->taskRepo->find($id);
        $task->setName($data["name"])
            ->setChecked($data["checked"]);

        $manager->flush();

        return new JsonResponse([
            'task' => $task,
        ]);
    }

    /**
     * @Route("/api/task/{id}", name="api_task_delete", methods={"DELETE"})
     */
    public function deleteTask(ObjectManager $manager, $id)
    {
        $task = $this->taskRepo->find($id);
        if($task == null ){
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
