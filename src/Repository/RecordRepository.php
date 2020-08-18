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
     * @param string|null $artist
     * @param string|null $title
     * @param int|null $year
     * @return array|Record[]
     */
    public function findAllWithArtist(
        int $limit,
        int $offset,
        ?string $artist = null,
        ?string $title = null,
        ?int $year = null
    ): array {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.artist', 'a')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->addOrderBy('a.name', 'asc')
            ->addOrderBy('r.title', 'asc');

        if (null !== $artist) {
            $queryBuilder
                ->andWhere('a.name LIKE :artist')
                ->setParameter('artist', "%$artist%");
        }
        if (null !== $title) {
            $queryBuilder
                ->andWhere('r.title LIKE :title')
                ->setParameter('title', "%$title%");
        }
        if (null !== $year) {
            $queryBuilder
                ->andWhere('r.year = :year')
                ->setParameter('year', $year);
        }

        return $queryBuilder
            ->getQuery()
            ->execute();
    }
}
