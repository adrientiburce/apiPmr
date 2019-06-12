<?php


namespace App\Controller\Api;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{

    private $repo;


    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }


    /**
     * @Route("/api/users", methods={"GET"})
     */
    public
    function getAllUsers()
    {
        $users = $this->repo->findAll();

        return new JsonResponse([
            'succes' => true,
            'users' => $users,
        ], 200);
    }

    /**
     * @Route("/api/users/{id}", methods={"GET"})
     */
    public
    function getOneUser($id)
    {
        $user = $this->repo->find($id);

        return new JsonResponse([
            'succes' => true,
            'user' => $user,
        ], 200);
    }
}