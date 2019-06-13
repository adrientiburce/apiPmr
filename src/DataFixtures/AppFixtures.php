<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Todos;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /** @var UserPasswordEncoderInterface * */
    private $passwordEncoder;

    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const USER_REFERENCE = 'user-';

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $hash = $this->passwordEncoder->encodePassword($user, 'user');
            $user->setPassword($hash);
            $user->setPseudo($faker->userName);

            for ($j = 0; $j < mt_rand(2, 4); $j++) {
                $todo = new Todos();
                $todo->setName($faker->city)
                    ->setUser($user);

                for ($l = 0; $l < mt_rand(2, 3); $l++) {
                    $task = new Task();
                    $task->setName($faker->sentence(3))
                        ->setChecked(rand(0, 1))
                        ->setUrl($faker->url)
                        ->setTodos($todo);
                    $manager->persist($task);
                }
                $manager->persist($todo);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }

}
