<?php

namespace App\Tests\Controller;

use App\Controller\ArtistController;
use App\Entity\Artist;
use App\Factory\JsonResponseFactory;
use App\Service\ArtistService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArtistControllerTest extends TestCase
{
    /**
     * @var MockObject|ArtistService
     */
    private MockObject $artistServiceMock;

    /**
     * @var MockObject|JsonResponseFactory
     */
    private MockObject $responseFactoryMock;

    /**
     * @var ArtistController
     */
    private ArtistController $controller;

    public function setUp()
    {
        $this->artistServiceMock = $this->createMock(ArtistService::class);
        $this->responseFactoryMock = $this->createMock(JsonResponseFactory::class);
        $this->controller = new ArtistController($this->artistServiceMock, $this->responseFactoryMock);
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
        /** @var MockObject|JsonResponse $responseMock */
        $responseMock = $this->createMock(JsonResponse::class);

        $inputBag = new InputBag(['limit' => 10, 'offset' => 20]);

        /** @var MockObject|Request $requestMock */
        $requestMock = $this->createMock(Request::class);
        $requestMock->query = $inputBag;

        $this->artistServiceMock->expects(self::once())->method('findAll');
        $this->responseFactoryMock
            ->expects(self::once())
            ->method('createJsonResponse')
            ->willReturn($responseMock);

        self::assertSame($responseMock, $this->controller->getBulkAction($requestMock));
    }
}
