<?php

namespace App\Tests\Service;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use App\Request\GenericFilterRequest;
use App\Service\ArtistService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ArtistServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface|MockObject
     */
    private MockObject $entityManagerMock;

    /**
     * @var ArtistRepository|MockObject
     */
    private MockObject $repositoryMock;

    /**
     * @var ArtistService
     */
    private ArtistService $artistService;

    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->repositoryMock = $this->createMock(ArtistRepository::class);
        $this->artistService = new ArtistService($this->entityManagerMock, $this->repositoryMock);
    }

    public function testDelete(): void
    {
        /** @var MockObject|Artist $mockData */
        $mockData = $this->createMock(Artist::class);
        $this->entityManagerMock->expects(self::at(0))->method('remove')->with($mockData);
        $this->entityManagerMock->expects(self::at(1))->method('flush')->with();

        $this->artistService->delete($mockData);
    }

    public function testFindAll(): void
    {
        /** @var MockObject|GenericFilterRequest $requestMock */
        $requestMock = $this->createMock(GenericFilterRequest::class);

        $this->repositoryMock->expects(self::once())->method('findBy')->willReturn([]);

        self::assertIsArray($this->artistService->findAll($requestMock));
    }
}
