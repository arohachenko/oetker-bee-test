<?php

namespace App\Tests\Service;

use App\Entity\Artist;
use App\Exception\ValidationException;
use App\Repository\ArtistRepository;
use App\Request\GenericFilterRequest;
use App\Request\SaveArtistRequest;
use App\Service\ArtistService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var MockObject|ValidatorInterface
     */
    private MockObject $validatorMock;

    /**
     * @var ArtistService
     */
    private ArtistService $artistService;

    public function setUp()
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->repositoryMock = $this->createMock(ArtistRepository::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->artistService = new ArtistService($this->entityManagerMock, $this->repositoryMock, $this->validatorMock);
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

    public function testUpdate(): void
    {
        /** @var MockObject|Artist $entityMock */
        $entityMock = $this->createMock(Artist::class);
        /** @var MockObject|SaveArtistRequest $requestMock */
        $requestMock = $this->createMock(SaveArtistRequest::class);
        $requestMock->method('getName')->willReturn('');

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(0);
        $this->validatorMock->method('validate')->with($entityMock)->willReturn($violationsMock);

        $this->entityManagerMock->expects(self::at(0))->method('persist')->with($entityMock);
        $this->entityManagerMock->expects(self::at(1))->method('flush');

        self::assertSame($entityMock, $this->artistService->update($entityMock, $requestMock));
    }

    public function testUpdateInvalid(): void
    {
        /** @var MockObject|Artist $entityMock */
        $entityMock = $this->createMock(Artist::class);
        /** @var MockObject|SaveArtistRequest $requestMock */
        $requestMock = $this->createMock(SaveArtistRequest::class);
        $requestMock->method('getName')->willReturn('');

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(1);
        $this->validatorMock->method('validate')->with($entityMock)->willReturn($violationsMock);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        self::expectException(ValidationException::class);
        $this->artistService->update($entityMock, $requestMock);
    }

    public function testCreate(): void
    {
        /** @var MockObject|SaveArtistRequest $requestMock */
        $requestMock = $this->createMock(SaveArtistRequest::class);
        $requestMock->method('getName')->willReturn('');

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(0);
        $this->validatorMock->method('validate')->willReturn($violationsMock);

        $this->entityManagerMock
            ->expects(self::at(0))
            ->method('persist')
            ->with(self::isInstanceOf(Artist::class));
        $this->entityManagerMock->expects(self::at(1))->method('flush');

        self::assertInstanceOf(Artist::class, $this->artistService->create($requestMock));
    }

    public function testCreateInvalid(): void
    {
        /** @var MockObject|SaveArtistRequest $requestMock */
        $requestMock = $this->createMock(SaveArtistRequest::class);
        $requestMock->method('getName')->willReturn('');

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(1);
        $this->validatorMock->method('validate')->willReturn($violationsMock);

        $this->entityManagerMock->expects(self::never())->method('persist');
        $this->entityManagerMock->expects(self::never())->method('flush');

        self::expectException(ValidationException::class);
        $this->artistService->create($requestMock);
    }
}
