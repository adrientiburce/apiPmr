<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EntrypointController extends AbstractController
{
    /**
     * @Route("/api", name="api_entrypoint")
     */
    public function index()
    {
        return new JsonResponse([
            "succes" => true,
        ], 200);
    }

    /**
     * @Route("/api/projects", name="api_projects_list")
     */
    public function projects()
    {
        // the user will never have ROLE_SUPER_ADMIN role
        return new JsonResponse([
            "succes" => false,
        ], 400);
    }
}
