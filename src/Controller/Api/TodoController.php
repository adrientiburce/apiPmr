<?php

namespace App\Controller\Api;

use App\Entity\Todos;
use App\Repository\TodosRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{

    private $todoRepo;

    public function __construct(TodosRepository $repo)
    {
        $this->todoRepo = $repo;
    }

    function getHeaderOrQueryData($request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            return is_array($data) ? $data : array();
        } else {
            return array(
                "name" => $request->query->get('name'),
            );
        }
    }


    /**
     * @Route("/api/lists", name="api_get_todos",  methods={"GET"})
     */
    public
    function getAllTodo()
    {
        $user = $this->getUser();
        $todos = $this->todoRepo->findAllByUser($user);

        return new JsonResponse([
            'lists' => $todos,
        ], 200);
    }

    /**
     * @Route("/api/lists/{id}", methods={"GET"})
     */
    public
    function getOneTodo($id)
    {
        $todo = $this->todoRepo->findOneByUser($id, $this->getUser());
        if ($todo == null) {
            return new JsonResponse([
                'success' => false,
                'todo' => "Not Found",
            ], 400);
        }

        return new JsonResponse([
            'list' => $todo,
        ], 200);
    }

    /**
     * @Route("/api/lists/{id}/items", name="api_get_todo", methods={"GET"}, requirements={"id"="\d+"})
     */
    public
    function getOneTodoWithItems($id): JsonResponse
    {
        $todo = $this->todoRepo->findOneByUser($id, $this->getUser());
        if ($todo == null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }

        $serializer = $this->get('serializer');
        $tasks = $todo->getTasks();
        return new JsonResponse([
            'success' => true,
            'list' => $todo->getName(),
            'items' => $serializer->normalize($tasks),
        ]);
    }

    /**
     * @Route("/api/lists", name="api_create_todo", methods={"POST"})
     */
    public
    function createTodo(Request $request, ObjectManager $manager): JsonResponse
    {
        $user = $this->getUser();
        $data = $this->getHeaderOrQueryData($request);
        if ($data["name"] == null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        $todo = new Todos();
        $todo->setName($data["name"])
            ->setUser($user);

        $manager->persist($todo);
        $manager->flush();

        return new JsonResponse([
            'success' => true,
            'list' => $todo,
        ], 200);
    }

    /**
     * @Route("/api/lists/{id}", methods={"PUT"}, requirements={"id"="\d+"})
     */
    public
    function updateTodo(ObjectManager $manager, Request $request, $id)
    {
        $data = $this->getHeaderOrQueryData($request);
        $todo = $this->todoRepo->findOneByUser($id, $this->getUser());

        if ($todo == null OR !is_string($data['name'])) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        $todo->setName($data['name']);
        $manager->flush();

        return new JsonResponse([
            'todo' => $todo,
        ]);
    }

    /**
     * @Route("/api/lists/{id}", name="api_todo_delete", methods={"DELETE"})
     */
    public
    function deleteTodo(ObjectManager $manager, $id)
    {
        $todo = $this->todoRepo->findOneByUser($id, $this->getUser());
        if ($todo == null) {
            return new JsonResponse([
                'success' => false,
                'todo' => "Not Found",
            ], 400);
        }

        $manager->remove($todo);
        $manager->flush();

        return new JsonResponse([
            'success' => true,
            'delete' => $todo->getName()
        ], 200);
    }
}