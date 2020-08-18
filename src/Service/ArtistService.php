<?php

namespace App\Service;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use App\Request\GenericFilterRequest;
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

    /**
     * @param GenericFilterRequest $request
     * @return array|Artist[]
     */
    public function findAll(GenericFilterRequest $request): array
    {
        return $this->artistRepository->findBy(
            [],
            ['name' => 'asc'],
            (int)$request->getLimit(),
            (int)$request->getOffset()
        );
    }
}
