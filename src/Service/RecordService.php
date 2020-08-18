<?php

namespace App\Service;

use App\Entity\Record;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecordService
{
    private EntityManagerInterface $entityManager;

    private RecordRepository $recordRepository;

    public function __construct(EntityManagerInterface $entityManager, RecordRepository $recordRepository)
    {
        $this->entityManager = $entityManager;
        $this->recordRepository = $recordRepository;
    }

    public function delete(Record $record): void
    {
        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array|Record[]
     */
    public function findAll(int $limit, int $offset): array
    {
        return $this->recordRepository->findAllWithArtist($limit, $offset);
    }
}
