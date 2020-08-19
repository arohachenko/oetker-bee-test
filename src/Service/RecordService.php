<?php

namespace App\Service;

use App\Entity\Artist;
use App\Entity\Record;
use App\Repository\ArtistRepository;
use App\Repository\RecordRepository;
use App\Request\GenericFilterRequest;
use App\Request\SaveArtistRequest;
use App\Request\SaveRecordRequest;
use Doctrine\ORM\EntityManagerInterface;

class RecordService
{
    private EntityManagerInterface $entityManager;

    private RecordRepository $recordRepository;

    private ArtistRepository $artistRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        RecordRepository $recordRepository,
        ArtistRepository $artistRepository
    ) {
        $this->entityManager = $entityManager;
        $this->recordRepository = $recordRepository;
        $this->artistRepository = $artistRepository;
    }

    public function delete(Record $record): void
    {
        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }

    /**
     * @param GenericFilterRequest $request
     * @return array|Record[]
     */
    public function findAll(GenericFilterRequest $request): array
    {
        return $this->recordRepository->findAllWithArtist(
            (int)$request->getLimit(),
            (int)$request->getOffset(),
            $request->getArtist(),
            $request->getTitle(),
            null === $request->getYear() ? null : (int)$request->getYear()
        );
    }

    public function update(Record $record, SaveRecordRequest $request): Record
    {
        $this->translateFromRequest($record, $request);

        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return $record;
    }

    public function create(SaveRecordRequest $request): Record
    {
        $record = new Record();
        $this->translateFromRequest($record, $request);

        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return $record;
    }

    private function translateFromRequest(Record $record, SaveRecordRequest $request): void
    {
        $record
            ->setTitle($request->getTitle())
            ->setType($request->getType())
            ->setLabel($request->getLabel())
            ->setYear((int)$request->getYear())
            ->setArtist($this->makeArtistRelation($request->getArtist()));
    }

    private function makeArtistRelation(SaveArtistRequest $request): Artist
    {
        return $this->artistRepository->findOneBy(['name' => $request->getName()])
            ?? (new Artist())->setName($request->getName());
    }
}
