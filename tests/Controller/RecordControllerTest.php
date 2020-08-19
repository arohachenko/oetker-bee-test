<?php

namespace App\Tests\Controller;

use App\Controller\RecordController;
use App\Entity\Record;
use App\Exception\ValidationException;
use App\Factory\JsonResponseFactory;
use App\Factory\RequestFactory;
use App\Request\GenericFilterRequest;
use App\Request\SaveRecordRequest;
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

    public function testPutAction(): void
    {
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $content = 'test';
        $httpRequestMock->method('getContent')->willReturn($content);
        /** @var MockObject|SaveRecordRequest $requestMock */
        $requestMock = $this->createMock(SaveRecordRequest::class);
        $this->requestFactoryMock
            ->method('createFromJsonBody')
            ->with($httpRequestMock)
            ->willReturn($requestMock);

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(0);

        $this->validatorMock
            ->expects(self::at(0))
            ->method('validate')
            ->with($content)
            ->willReturn($violationsMock);
        $this->validatorMock
            ->expects(self::at(1))
            ->method('validate')
            ->with($requestMock)
            ->willReturn($violationsMock);

        /** @var MockObject|Record $entityMock */
        $entityMock = $this->createMock(Record::class);
        $responseMock = $this->createMock(JsonResponse::class);

        $this->recordServiceMock->expects(self::once())->method('update')->with($entityMock, $requestMock);
        $this->responseFactoryMock->method('createJsonResponse')->willReturn($responseMock);

        self::assertSame($responseMock, $this->controller->putAction($httpRequestMock, $entityMock));
    }

    public function testPutActionNotFound(): void
    {
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);

        $this->recordServiceMock->expects(self::never())->method('update');

        self::expectException(NotFoundHttpException::class);
        $this->controller->putAction($httpRequestMock, null);
    }

    public function testPutActionInvalidJson(): void
    {
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $content = 'test';
        $httpRequestMock->method('getContent')->willReturn($content);
        /** @var MockObject|Record $entityMock */
        $entityMock = $this->createMock(Record::class);

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(1);

        $this->validatorMock
            ->method('validate')
            ->with($content)
            ->willReturn($violationsMock);

        $this->recordServiceMock->expects(self::never())->method('update');

        self::expectException(ValidationException::class);
        $this->controller->putAction($httpRequestMock, $entityMock);
    }

    public function testPutActionInvalidRequest(): void
    {
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $content = 'test';
        $httpRequestMock->method('getContent')->willReturn($content);
        /** @var MockObject|Record $entityMock */
        $entityMock = $this->createMock(Record::class);
        /** @var MockObject|SaveRecordRequest $requestMock */
        $requestMock = $this->createMock(SaveRecordRequest::class);
        $this->requestFactoryMock
            ->method('createFromJsonBody')
            ->willReturn($requestMock);

        /** @var MockObject|ConstraintViolationListInterface $goodViolationsMock */
        $goodViolationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $goodViolationsMock->method('count')->willReturn(0);

        /** @var MockObject|ConstraintViolationListInterface $goodViolationsMock */
        $badViolationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $badViolationsMock->method('count')->willReturn(1);

        $this->validatorMock
            ->expects(self::at(0))
            ->method('validate')
            ->with($content)
            ->willReturn($goodViolationsMock);
        $this->validatorMock
            ->expects(self::at(1))
            ->method('validate')
            ->with($requestMock)
            ->willReturn($badViolationsMock);

        $this->recordServiceMock->expects(self::never())->method('update');

        self::expectException(ValidationException::class);
        $this->controller->putAction($httpRequestMock, $entityMock);
    }

    public function testPostAction(): void
    {
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $content = 'test';
        $httpRequestMock->method('getContent')->willReturn($content);
        /** @var MockObject|SaveRecordRequest $requestMock */
        $requestMock = $this->createMock(SaveRecordRequest::class);
        $this->requestFactoryMock
            ->method('createFromJsonBody')
            ->with($httpRequestMock)
            ->willReturn($requestMock);

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(0);

        $this->validatorMock
            ->expects(self::at(0))
            ->method('validate')
            ->with($content)
            ->willReturn($violationsMock);
        $this->validatorMock
            ->expects(self::at(1))
            ->method('validate')
            ->with($requestMock)
            ->willReturn($violationsMock);

        $responseMock = $this->createMock(JsonResponse::class);

        $this->recordServiceMock->expects(self::once())->method('create')->with($requestMock);
        $this->responseFactoryMock->method('createJsonResponse')->willReturn($responseMock);

        self::assertSame($responseMock, $this->controller->postAction($httpRequestMock));
    }

    public function testPostActionInvalidJson(): void
    {
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $content = 'test';
        $httpRequestMock->method('getContent')->willReturn($content);

        /** @var MockObject|ConstraintViolationListInterface $violationsMock */
        $violationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $violationsMock->method('count')->willReturn(1);

        $this->validatorMock
            ->method('validate')
            ->with($content)
            ->willReturn($violationsMock);

        $this->recordServiceMock->expects(self::never())->method('create');

        self::expectException(ValidationException::class);
        $this->controller->postAction($httpRequestMock);
    }

    public function testPostActionInvalidRequest(): void
    {
        /** @var MockObject|Request $httpRequestMock */
        $httpRequestMock = $this->createMock(Request::class);
        $content = 'test';
        $httpRequestMock->method('getContent')->willReturn($content);
        /** @var MockObject|SaveRecordRequest $requestMock */
        $requestMock = $this->createMock(SaveRecordRequest::class);
        $this->requestFactoryMock
            ->method('createFromJsonBody')
            ->willReturn($requestMock);

        /** @var MockObject|ConstraintViolationListInterface $goodViolationsMock */
        $goodViolationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $goodViolationsMock->method('count')->willReturn(0);

        /** @var MockObject|ConstraintViolationListInterface $goodViolationsMock */
        $badViolationsMock = $this->createMock(ConstraintViolationListInterface::class);
        $badViolationsMock->method('count')->willReturn(1);

        $this->validatorMock
            ->expects(self::at(0))
            ->method('validate')
            ->with($content)
            ->willReturn($goodViolationsMock);
        $this->validatorMock
            ->expects(self::at(1))
            ->method('validate')
            ->with($requestMock)
            ->willReturn($badViolationsMock);

        $this->recordServiceMock->expects(self::never())->method('create');

        self::expectException(ValidationException::class);
        $this->controller->postAction($httpRequestMock);
    }
}
