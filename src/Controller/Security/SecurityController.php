<?php

namespace App\Controller\Security;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    function getHeaderOrQueryData($request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            return is_array($data) ? $data : array();
        } else {
            return array(
                "username" => $request->query->get('username'),
                "password" => $request->query->get('password'),
            );
        }
    }

    /**
     * @Route("/api/authenticate", name="api_login", methods={"POST"})
     */
    public function login()
    {
        $user = $this->getUser();

        return new JsonResponse([
            'hash' => $user->getApiToken(),
            'pseudo' => $user->getPseudo(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/api/register", methods={"POST"})
     */
    public
    function registerUser(Request $request, ObjectManager $manager)
    {

        $data = $this->getHeaderOrQueryData($request);
        if (!is_string($data["username"]) && !is_string($data["password"])) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        if ($userRepo->findBy(array("pseudo" => $data["username"])) != null) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }


        $user = new User();
        $hash = $this->passwordEncoder->encodePassword($user, $data["password"]);
        $user->setPassword($hash);
        $user->setPseudo($data["username"]);

        $manager->persist($user);
        $manager->flush();

        return new JsonResponse([
            'succes' => true,
            'user' => $user,
        ], 200);
    }

    /**
     * @Route("/api/users", methods={"POST"})
     */
    public
    function updateUserPassword(Request $request, ObjectManager $manager)
    {

        $user = $this->getUser();

        $data = $this->getHeaderOrQueryData($request);
        if (!is_string($data["password"])) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }

        $hash = $this->passwordEncoder->encodePassword($user, $data["password"]);
        $user->setPassword($hash);

        $manager->flush();

        return new JsonResponse([
            'succes' => true,
            'user' => $user,
        ], 200);
    }

}
