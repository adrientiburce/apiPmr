<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setName("Tache n°" . $i)
                ->setChecked(false);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
