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
                "label" => $request->query->get('label'),
                "url" => $request->query->get('url'),
                "check" => $request->query->get('check'),
            );
        }
    }

    /**
     * @Route("/myapi/lists/{idList}/items/{idTask}", methods={"GET"}, requirements={"id"="\d+", "idTask"="\d+"})
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
     * @Route("/myapi/lists/{id}/items", methods={"POST"})
     */
    public function createTask($id, Request $request, ObjectManager $manager): JsonResponse
    {
        $data = $this->getHeaderOrQueryData($request);
        if ($data['label'] == null) {
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
        $task->setName($data["label"])
            ->setChecked(0)
            ->setTodos($todo);

        if (preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i',
            $data["url"])) {
            $task->setUrl($data["url"]);
        }

        $manager->persist($task);
        $manager->flush();

        return new JsonResponse([
            "success" => true,
            'item' => $task,
        ], 200);
    }

    /**
     * @Route("/myapi/lists/{idList}/items/{idTask}", methods={"PUT"})
     */
    public function checkTask(ObjectManager $manager, Request $request, $idList, $idTask)
    {
        $data = $this->getHeaderOrQueryData($request);
        if ($data["check"] == null) {
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
        if (($data["check"] == 0 OR $data["check"] = 1) AND ($task->getChecked() != $data["check"])) {
                $task->setChecked(intval($data["check"]));
        }else{
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        $manager->flush();

        return new JsonResponse([
            'success' => true,
            'task' => $task,
        ]);
    }

    /**
     * @Route("/myapi/lists/{idList}/items/{idTask}", methods={"DELETE"})
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
