<?php

namespace App\Tests\Controller;

use App\Controller\RecordController;
use App\Entity\Record;
use App\Service\RecordService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecordControllerTest extends TestCase
{
    /**
     * @var MockObject|RecordService
     */
    private MockObject $recordServiceMock;

    /**
     * @var RecordController
     */
    private RecordController $controller;

    public function setUp()
    {
        $this->recordServiceMock = $this->createMock(RecordService::class);
        $this->controller = new RecordController($this->recordServiceMock);
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
}
