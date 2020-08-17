<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\SymfonyEventsSubscriber;
use App\Factory\JsonResponseFactory;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SymfonyEventsSubscriberTest extends TestCase
{
    /**
     * @var MockObject|JsonResponseFactory
     */
    private MockObject $responseFactoryMock;

    /**
     * @var SymfonyEventsSubscriber
     */
    private SymfonyEventsSubscriber $subscriber;

    public function setUp()
    {
        $this->responseFactoryMock = $this->createMock(JsonResponseFactory::class);
        $this->subscriber = new SymfonyEventsSubscriber($this->responseFactoryMock);
    }

    public function testGetSubscribedEvents()
    {
        self::assertIsArray(SymfonyEventsSubscriber::getSubscribedEvents());
    }

    public function testConvertErrorToJson()
    {
        $exception = new Exception;
        $response = new JsonResponse();

        /** @var MockObject|Request $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getAcceptableContentTypes')->willReturn(['test', 'application/json', null]);

        /** @var MockObject|HttpKernelInterface $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        // ExceptionEvent is final, thanks Fabien
        $event = new ExceptionEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->responseFactoryMock
            ->expects(self::once())
            ->method('createErrorResponse')
            ->with($exception)
            ->willReturn($response);

        $this->subscriber->convertErrorToJson($event);

        self::assertSame($response, $event->getResponse());
    }

    public function testConvertErrorToJsonNotApplicable()
    {
        $exception = new Exception;

        /** @var MockObject|Request $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getAcceptableContentTypes')->willReturn([]);

        /** @var MockObject|HttpKernelInterface $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        // ExceptionEvent is final, thanks Fabien
        $event = new ExceptionEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->responseFactoryMock->expects(self::never())->method('createErrorResponse');

        $this->subscriber->convertErrorToJson($event);
    }
}
