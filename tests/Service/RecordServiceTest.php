<?php

namespace App\Tests\Service;

use App\Entity\Record;
use App\Repository\ArtistRepository;
use App\Repository\RecordRepository;
use App\Request\GenericFilterRequest;
use App\Request\SaveArtistRequest;
use App\Request\SaveRecordRequest;
use App\Service\RecordService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecordServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface|MockObject
     */
    private MockObject $entityManagerMock;

    /**
     * @var RecordRepository|MockObject
     */
    private MockObject $repositoryMock;

    /**
     * @var ArtistRepository|MockObject
     */
    private MockObject $artistRepositoryMock;

    /**
     * @var RecordService
     */
    private RecordService $recordService;

    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->repositoryMock = $this->createMock(RecordRepository::class);
        $this->artistRepositoryMock = $this->createMock(ArtistRepository::class);
        $this->recordService = new RecordService(
            $this->entityManagerMock,
            $this->repositoryMock,
            $this->artistRepositoryMock
        );
    }

    public function testDelete(): void
    {
        /** @var MockObject|Record $mockData */
        $mockData = $this->createMock(Record::class);
        $this->entityManagerMock->expects(self::at(0))->method('remove')->with($mockData);
        $this->entityManagerMock->expects(self::at(1))->method('flush')->with();

        $this->recordService->delete($mockData);
    }

    public function testFindAll(): void
    {
        /** @var MockObject|GenericFilterRequest $requestMock */
        $requestMock = $this->createMock(GenericFilterRequest::class);

        $this->repositoryMock->expects(self::once())->method('findAllWithArtist')->willReturn([]);

        self::assertIsArray($this->recordService->findAll($requestMock));
    }

    public function testUpdate(): void
    {
        /** @var MockObject|Record $entityMock */
        $entityMock = $this->createMock(Record::class);
        $requestMock = $this->mockSaveRequest();

        $this->entityManagerMock->expects(self::at(0))->method('persist')->with($entityMock);
        $this->entityManagerMock->expects(self::at(1))->method('flush')->with();

        self::assertSame($entityMock, $this->recordService->update($entityMock, $requestMock));
    }

    public function testCreate(): void
    {
        $requestMock = $this->mockSaveRequest();

        $this->entityManagerMock
            ->expects(self::at(0))
            ->method('persist')
            ->with(self::isInstanceOf(Record::class));
        $this->entityManagerMock->expects(self::at(1))->method('flush')->with();

        self::assertInstanceOf(Record::class, $this->recordService->create($requestMock));
    }

    private function mockSaveRequest(): SaveRecordRequest
    {
        $artistRequest = $this->createMock(SaveArtistRequest::class);
        $artistRequest->method('getName')->willReturn('artistName');
        /** @var MockObject|SaveRecordRequest $request */
        $request = $this->createMock(SaveRecordRequest::class);
        $request->method('getTitle')->willReturn('');
        $request->method('getType')->willReturn('');
        $request->method('getLabel')->willReturn('');
        $request->method('getYear')->willReturn(2020);
        $request->method('getArtist')->willReturn($artistRequest);

        return $request;
    }
}
