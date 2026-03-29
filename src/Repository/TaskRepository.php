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
        $allowedSorts = ['dueDate', 'updatedAt', 'title', 'priority'];
        if (!in_array($sortField, $allowedSorts, true)) {
            $sortField = 'updatedAt';
        }

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'DESC';
        }

        $qb = $this->createQueryBuilder('t');

        if ($sortField === 'priority') {
            $qb->addSelect(
                '(
                CASE
                    WHEN t.priority = :high THEN 1
                    WHEN t.priority = :medium THEN 2
                    WHEN t.priority = :low THEN 3
                ELSE 99
                END
                ) AS HIDDEN priorityRank'
            )
            ->setParameter('high', 'high')
            ->setParameter('medium', 'medium')
            ->setParameter('low', 'low')
            ->orderBy('priorityRank', $direction);
        } else {
            $qb->orderBy('t.' . $sortField, $direction);
        }

        if ('updatedAt' !== $sortField) {
            $qb->addOrderBy('t.updatedAt', 'DESC');
        }
        var_dump(
            $qb->getDQL()
        );
        // var_dump($sortField, $direction);
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
