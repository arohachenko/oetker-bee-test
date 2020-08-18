<?php

namespace App\Tests\Controller;

use App\Controller\ArtistController;
use App\Entity\Artist;
use App\Exception\ValidationException;
use App\Factory\JsonResponseFactory;
use App\Factory\RequestFactory;
use App\Request\GenericFilterRequest;
use App\Service\ArtistService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArtistControllerTest extends TestCase
{
    /**
     * @var MockObject|ArtistService
     */
    private MockObject $artistServiceMock;

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
     * @var ArtistController
     */
    private ArtistController $controller;

    public function setUp()
    {
        $this->artistServiceMock = $this->createMock(ArtistService::class);
        $this->requestFactoryMock = $this->createMock(RequestFactory::class);
        $this->responseFactoryMock = $this->createMock(JsonResponseFactory::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->controller = new ArtistController(
            $this->artistServiceMock,
            $this->requestFactoryMock,
            $this->responseFactoryMock,
            $this->validatorMock
        );
    }

    public function testDeleteAction(): void
    {
        /** @var MockObject|Artist $artistMock */
        $artistMock = $this->createMock(Artist::class);

        $this->artistServiceMock->expects(self::once())->method('delete')->with($artistMock);

        $response = $this->controller->deleteAction($artistMock);
        self::assertSame(204, $response->getStatusCode());
    }

    public function testDeleteNotFound(): void
    {
        self::expectException(NotFoundHttpException::class);

        $this->controller->deleteAction(null);
    }

    public function testGetAction(): void
    {
        /** @var MockObject|Artist $artistMock */
        $artistMock = $this->createMock(Artist::class);
        /** @var MockObject|JsonResponse $responseMock */
        $responseMock = $this->createMock(JsonResponse::class);

        $this->responseFactoryMock
            ->expects(self::once())
            ->method('createJsonResponse')
            ->with($artistMock)
            ->willReturn($responseMock);

        self::assertSame($responseMock, $this->controller->getAction($artistMock));
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

        $this->artistServiceMock->expects(self::once())->method('findAll')->with($requestMock);
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

        $this->artistServiceMock->expects(self::never())->method('findAll');
        $this->responseFactoryMock->expects(self::never())->method('createJsonResponse');

        self::expectException(ValidationException::class);
        $this->controller->getBulkAction($httpRequestMock);
    }
}
