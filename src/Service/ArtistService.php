<?php

namespace App\Service;

use App\Entity\Artist;
use App\Exception\ValidationException;
use App\Repository\ArtistRepository;
use App\Request\GenericFilterRequest;
use App\Request\SaveArtistRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArtistService
{
    private EntityManagerInterface $entityManager;

    private ArtistRepository $artistRepository;

    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ArtistRepository $artistRepository,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->artistRepository = $artistRepository;
        $this->validator = $validator;
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

    public function updateArtist(Artist $artist, SaveArtistRequest $request): Artist
    {
        $artist->setName($request->getName());

        $violations = $this->validator->validate($artist);
        if (0 !== count($violations)) {
            throw new ValidationException($violations);
        }

        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        return $artist;
    }

    public function createArtist(SaveArtistRequest $request): Artist
    {
        $artist = (new Artist())->setName($request->getName());

        $violations = $this->validator->validate($artist);
        if (0 !== count($violations)) {
            throw new ValidationException($violations);
        }

        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        return $artist;
    }
}
