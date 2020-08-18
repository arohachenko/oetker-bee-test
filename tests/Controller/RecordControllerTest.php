<?php

namespace App\Tests\Controller;

use App\Controller\RecordController;
use App\Entity\Record;
use App\Factory\JsonResponseFactory;
use App\Service\RecordService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecordControllerTest extends TestCase
{
    /**
     * @var MockObject|RecordService
     */
    private MockObject $recordServiceMock;

    /**
     * @var MockObject|JsonResponseFactory
     */
    private MockObject $responseFactoryMock;

    /**
     * @var RecordController
     */
    private RecordController $controller;

    public function setUp()
    {
        $this->recordServiceMock = $this->createMock(RecordService::class);
        $this->responseFactoryMock = $this->createMock(JsonResponseFactory::class);
        $this->controller = new RecordController($this->recordServiceMock, $this->responseFactoryMock);
    }

    public function testDeleteAction(): void
    {
        /** @var MockObject|Record $recordMock */
        $recordMock = $this->createMock(Record::class);

        $this->recordServiceMock->expects(self::once())->method('delete')->with($recordMock);

        $response = $this->controller->deleteAction($recordMock);
        self::assertSame(204, $response->getStatusCode());
    }

    public function testDeleteNotFound(): void
    {
        self::expectException(NotFoundHttpException::class);

        $this->controller->deleteAction(null);
    }

    public function testGetAction(): void
    {
        /** @var MockObject|Record $recordMock */
        $recordMock = $this->createMock(Record::class);
        /** @var MockObject|JsonResponse $responseMock */
        $responseMock = $this->createMock(JsonResponse::class);

        $this->responseFactoryMock
            ->expects(self::once())
            ->method('createJsonResponse')
            ->with($recordMock)
            ->willReturn($responseMock);

        self::assertSame($responseMock, $this->controller->getAction($recordMock));
    }

    public function testGetActionNotFound(): void
    {
        $this->responseFactoryMock->expects(self::never())->method('createJsonResponse');

        self::expectException(NotFoundHttpException::class);
        $this->controller->getAction(null);
    }

    public function testGetBulkAction(): void
    {
        /** @var MockObject|JsonResponse $responseMock */
        $responseMock = $this->createMock(JsonResponse::class);

        $inputBag = new InputBag(['limit' => 10, 'offset' => 20]);

        /** @var MockObject|Request $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->query = $inputBag;

        $this->recordServiceMock->expects(self::once())->method('findAll');
        $this->responseFactoryMock
            ->expects(self::once())
            ->method('createJsonResponse')
            ->willReturn($responseMock);

        self::assertSame($responseMock, $this->controller->getBulkAction($requestMock));
    }
}
