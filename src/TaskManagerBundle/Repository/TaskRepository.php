<?php

namespace TaskManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use TaskManagerBundle\Entity\User;

/**
 * TaskRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaskRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('dueDate' => 'ASC'));
    }

    public function getAllNotClosedTasks(User $user)
    {
        $query = $this->createQueryBuilder('t')
            ->where("t.status != 'Closed'")
            ->andWhere('t.user = :user')
            ->orderBy('t.dueDate', 'ASC')
            ->setParameter('user', $user)
            ->getQuery();
        return $query->getResult();
    }

}
