<?php

namespace App\Service;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;

class ArtistService
{
    private EntityManagerInterface $entityManager;

    private ArtistRepository $artistRepository;

    public function __construct(EntityManagerInterface $entityManager, ArtistRepository $artistRepository)
    {
        $this->entityManager = $entityManager;
        $this->artistRepository = $artistRepository;
    }

    public function delete(Artist $artist): void
    {
        $this->entityManager->remove($artist);
        $this->entityManager->flush();
    }
}