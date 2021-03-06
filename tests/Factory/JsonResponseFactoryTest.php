<?php

namespace App\Tests\Factory;

use App\Entity\Artist;
use App\Exception\ValidationException;
use App\Factory\JsonResponseFactory;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class JsonResponseFactoryTest extends TestCase
{
    /**
     * @var MockObject|SerializerInterface
     */
    private MockObject $serializerMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private MockObject $loggerMock;

    /**
     * @var JsonResponseFactory
     */
    private JsonResponseFactory $jsonResponseFactory;

    public function setUp()
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->jsonResponseFactory = new JsonResponseFactory($this->serializerMock);
        $this->jsonResponseFactory->setLogger($this->loggerMock);
    }

    public function testCreateErrorResponseFromGenericException(): void
    {
        $exception = new Exception();
        $this->serializerMock->method('serialize')->willReturn('');
        $this->loggerMock->expects(self::once())->method('warning');

        $response = $this->jsonResponseFactory->createErrorResponse($exception);
        self::assertSame(500, $response->getStatusCode());
    }

    public function testCreateErrorResponseFromHttpException(): void
    {
        $exception = new HttpException(403);
        $this->serializerMock->method('serialize')->willReturn('');
        $this->loggerMock->expects(self::once())->method('warning');

        $response = $this->jsonResponseFactory->createErrorResponse($exception);
        self::assertSame(403, $response->getStatusCode());
    }

    public function testCreateErrorResponseFromValidationException(): void
    {
        /** @var MockObject|ConstraintViolationList $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationList::class);
        $exception = new ValidationException($violationsMock);
        $this->serializerMock
            ->expects(self::at(1))
            ->method('serialize')
            ->with($violationsMock)
            ->willReturn('');
        $this->loggerMock->expects(self::once())->method('warning');

        $response = $this->jsonResponseFactory->createErrorResponse($exception);
        self::assertSame(422, $response->getStatusCode());
    }

    public function testCreateJsonResponseFromObject(): void
    {
        $data = $this->createMock(Artist::class);
        $serializedData = '{"jsonstring"}';
        $code = 418;

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with($data)
            ->willReturn($serializedData);

        $response = $this->jsonResponseFactory->createJsonResponse($data, null, $code);
        self::assertSame($serializedData, $response->getContent());
        self::assertSame($code, $response->getStatusCode());
    }

    public function testCreateJsonResponseFromArray(): void
    {
        $data = [123, $this->createMock(Artist::class)];
        $serializedData = '{"jsonstring"}';
        $code = 418;

        $this->serializerMock
            ->expects(self::once())
            ->method('serialize')
            ->with($data)
            ->willReturn($serializedData);

        $response = $this->jsonResponseFactory->createJsonResponse($data, null, $code);
        self::assertSame($serializedData, $response->getContent());
        self::assertSame($code, $response->getStatusCode());
    }
}
