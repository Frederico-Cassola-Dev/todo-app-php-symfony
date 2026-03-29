<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findAllSorted(string $sortField, string $direction): array
    {
        $allowedSorts = ['dueDate', 'updatedAt', 'title'];
        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'updatedAt';
        }

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'DESC';
        }

        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.' . $sortField, $direction);

        if ('updatedAt' !== $sortField) {
            $qb->addOrderBy('t.updatedAt', 'DESC');
        }
        var_dump($qb->getDQL());

        return $qb->getQuery()
        ->getResult();
    }

    public function sortByDueDate(): array
    {
        return $this->createQueryBuilder('t')
        ->orderBy('t.dueDate', 'DESC')
        ->getQuery()
        ->getResult();
    }
}
