<?php

namespace App\Service;

use App\Entity\Record;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecordService
{
    private EntityManagerInterface $entityManager;

    private ArtistRepository $artistRepository;

    public function __construct(EntityManagerInterface $entityManager, ArtistRepository $artistRepository)
    {
        $this->entityManager = $entityManager;
        $this->artistRepository = $artistRepository;
    }

    public function delete(Record $record): void
    {
        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }
}
