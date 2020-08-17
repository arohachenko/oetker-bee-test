<?php

namespace App\Controller;

use App\Entity\Record;
use App\Service\RecordService;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/records")
 * @SWG\Tag(name="Records")
 */
class RecordController
{
    private RecordService $recordService;

    public function __construct(RecordService $recordService)
    {
        $this->recordService = $recordService;
    }

    /**
     * @Route(methods={"DELETE"}, path="/{id<\d+>}")
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NO_CONTENT,
     *     description="Deletion successful",
     * )
     *
     * @param Record $record
     * @return JsonResponse
     */
    public function deleteAction(Record $record): JsonResponse
    {
        $this->recordService->delete($record);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
