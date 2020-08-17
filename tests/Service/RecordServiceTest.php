<?php

namespace App\Tests\Service;

use App\Entity\Record;
use App\Repository\RecordRepository;
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
     * @var RecordService
     */
    private RecordService $recordService;

    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->repositoryMock = $this->createMock(RecordRepository::class);
        $this->recordService = new RecordService($this->entityManagerMock, $this->repositoryMock);
    }

    public function testDelete()
    {
        /** @var MockObject|Record $mockData */
        $mockData = $this->createMock(Record::class);
        $this->entityManagerMock->expects(self::at(0))->method('remove')->with($mockData);
        $this->entityManagerMock->expects(self::at(1))->method('flush')->with();

        $this->recordService->delete($mockData);
    }
}
