<?php

namespace App\Tests\Repository;

use App\Repository\RecordRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecordRepositoryTest extends TestCase
{
    /** @var MockObject|QueryBuilder */
    private MockObject $queryBuilderMock;

    /**
     * @var MockObject|EntityManagerInterface
     */
    private MockObject $emMock;

    /**
     * @var RecordRepository
     */
    private RecordRepository $repository;

    public function setUp()
    {
        $classMetadataMock = $this->createMock(ClassMetadata::class);
        $this->queryBuilderMock = $this->createMock(QueryBuilder::class);

        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->emMock->method('getClassMetadata')->willReturn($classMetadataMock);
        $this->emMock->method('createQueryBuilder')->willReturn($this->queryBuilderMock);
        $this->emMock->method('getConfiguration')->willReturn(
            $this->createMock(Configuration::class)
        );

        /** @var MockObject|ManagerRegistry $managerRegistryMock */
        $managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $managerRegistryMock->method('getManagerForClass')->willReturn($this->emMock);

        $this->repository = new RecordRepository($managerRegistryMock);
    }

    public function testFindAllWithArtist(): void
    {
        $queryMock = $this->createMock(AbstractQuery::class);
        $queryMock->expects(self::once())->method('execute')->willReturn([]);

        $this->queryBuilderMock->method('getQuery')->willReturn($queryMock);
        $this->queryBuilderMock->method(self::matchesRegularExpression('/((?!getQuery).)*/'))->willReturnSelf();

        self::assertIsArray($this->repository->findAllWithArtist(1, 2));
    }
}
