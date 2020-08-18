<?php

namespace App\Tests\Controller;

use App\Controller\ArtistController;
use App\Entity\Artist;
use App\Service\ArtistService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArtistControllerTest extends TestCase
{
    /**
     * @var MockObject|ArtistService
     */
    private MockObject $artistServiceMock;

    /**
     * @var ArtistController
     */
    private ArtistController $controller;

    public function setUp()
    {
        $this->artistServiceMock = $this->createMock(ArtistService::class);
        $this->controller = new ArtistController($this->artistServiceMock);
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
}
