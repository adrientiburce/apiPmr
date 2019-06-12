<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Task::class);
    }


    public function findOneByUser($idList, $idTask, $user)
    {
        return $this->createQueryBuilder('task')
            ->andWhere('task.id = :id')
            ->setParameter('id', $idTask)
            ->innerJoin('task.todos', 'todoUser', 'WITH', 'todoUser.user = :user')
            ->setParameter('user', $user)
            ->innerJoin('task.todos', 'todoId', 'WITH', 'todoId.id = :idList')
            ->setParameter('idList', $idList)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
