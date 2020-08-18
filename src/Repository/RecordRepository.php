<?php

namespace App\Repository;

use App\Entity\Record;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array|Record[]
     */
    public function findAllWithArtist(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.artist', 'a')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->addOrderBy('a.name', 'asc')
            ->addOrderBy('r.title', 'asc')
            ->getQuery()
            ->execute();
    }
}
