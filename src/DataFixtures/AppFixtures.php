<?php

namespace App\DataFixtures;

use App\Entity\Task;
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

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $hash = $this->passwordEncoder->encodePassword($user, 'user');
            $user->setPassword($hash);
            $user->setPseudo($faker->userName);

            for ($j = 0; $j < mt_rand(1, 5); $j++) {
                $task = new Task();
                $task->setName($faker->sentence(3))
                    ->setChecked(rand(0, 1) == 1 ? true : false)
                    ->setUser($user);
                $manager->persist($task);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }

}
