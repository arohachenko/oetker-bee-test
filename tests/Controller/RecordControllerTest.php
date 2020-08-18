<?php

namespace App\Tests\Controller;

use App\Controller\RecordController;
use App\Entity\Record;
use App\Exception\ValidationException;
use App\Factory\JsonResponseFactory;
use App\Factory\RequestFactory;
use App\Request\GenericFilterRequest;
use App\Service\RecordService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecordControllerTest extends TestCase
{
    /**
     * @var MockObject|RecordService
     */
    private MockObject $recordServiceMock;

    /**
     * @var MockObject|RequestFactory
     */
    private MockObject $requestFactoryMock;

    /**
     * @var MockObject|JsonResponseFactory
     */
    private MockObject $responseFactoryMock;

    /**
     * @var MockObject|ValidatorInterface
     */
    private MockObject $validatorMock;

    /**
     * @var RecordController
     */
    private RecordController $controller;

    public function setUp()
    {
        $this->recordServiceMock = $this->createMock(RecordService::class);
        $this->requestFactoryMock = $this->createMock(RequestFactory::class);
        $this->responseFactoryMock = $this->createMock(JsonResponseFactory::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->controller = new RecordController(
            $this->recordServiceMock,
            $this->requestFactoryMock,
            $this->responseFactoryMock,
            $this->validatorMock
        );
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
        /** @var MockObject|GenericFilterRequest $requestMock */
        $requestMock = $this->createMock(GenericFilterRequest::class);
        /** @var MockObject|JsonResponse $responseMock */
        $responseMock = $this->createMock(JsonResponse::class);
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $this->requestFactoryMock
            ->method('createGenericFilterRequest')
            ->with($httpRequestMock)
            ->willReturn($requestMock);
        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(0);
        $this->validatorMock->method('validate')->with($requestMock)->willReturn($violationsMock);

        $this->recordServiceMock->expects(self::once())->method('findAll')->with($requestMock);
        $this->responseFactoryMock
            ->expects(self::once())
            ->method('createJsonResponse')
            ->willReturn($responseMock);

        self::assertSame($responseMock, $this->controller->getBulkAction($httpRequestMock));
    }

    public function testGetBulkInvalidAction(): void
    {
        /** @var MockObject|GenericFilterRequest $requestMock */
        $requestMock = $this->createMock(GenericFilterRequest::class);
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $this->requestFactoryMock
            ->method('createGenericFilterRequest')
            ->with($httpRequestMock)
            ->willReturn($requestMock);
        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(123);
        $this->validatorMock->method('validate')->with($requestMock)->willReturn($violationsMock);

        $this->recordServiceMock->expects(self::never())->method('findAll');
        $this->responseFactoryMock->expects(self::never())->method('createJsonResponse');

        self::expectException(ValidationException::class);
        $this->controller->getBulkAction($httpRequestMock);
    }
}
