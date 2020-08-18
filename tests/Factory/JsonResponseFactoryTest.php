<?php

namespace App\Tests\Factory;

use App\Factory\JsonResponseFactory;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;

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
        $exception = new HttpException(404);
        $this->serializerMock->method('serialize')->willReturn('');
        $this->loggerMock->expects(self::once())->method('warning');

        $response = $this->jsonResponseFactory->createErrorResponse($exception);
        self::assertSame(404, $response->getStatusCode());
    }
}
