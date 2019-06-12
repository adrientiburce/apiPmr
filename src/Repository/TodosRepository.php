<?php

namespace App\Repository;

use App\Entity\Todos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Todos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todos[]    findAll()
 * @method Todos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Todos::class);
    }

    public function findAllByUser($user)
    {
        return $this->createQueryBuilder('todo')
            ->andWhere('todo.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findOneByUser($id, $user)
    {
        return $this->createQueryBuilder('todo')
            ->andWhere('todo.user = :user')
            ->setParameter('user', $user)
            ->andWhere('todo.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /*
    public function findOneBySomeField($value): ?Todos
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
