<?php

namespace App\Repository;

use App\Entity\Solve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SolveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Solve::class);
    }

    /**
     * Returns rows like:
     * [
     *   ['email' => 'admin@...', 'fullName' => '...', 'totalPoints' => 420, 'solvedCount' => 3],
     *   ...
     * ]
     */
    public function getLeaderboard(): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.user', 'u')
            ->innerJoin('s.lab', 'l')
            ->select('u.email AS email')
            ->addSelect('u.fullName AS fullName')
            ->addSelect('SUM(l.points) AS totalPoints')
            ->addSelect('COUNT(s.id) AS solvedCount')
            ->groupBy('u.id')
            ->orderBy('totalPoints', 'DESC')
            ->addOrderBy('solvedCount', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
