<?php

namespace App\Controller;

use App\Entity\Record;
use App\Factory\JsonResponseFactory;
use App\Service\RecordService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/records")
 * @SWG\Tag(name="Records")
 */
class RecordController
{
    private const MESSAGE_NOT_FOUND = 'Record not found';

    private RecordService $recordService;

    private JsonResponseFactory $responseFactory;

    public function __construct(RecordService $recordService, JsonResponseFactory $responseFactory)
    {
        $this->recordService = $recordService;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @Route(methods={"DELETE"}, path="/{id<\d+>}")
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NO_CONTENT,
     *     description="Deletion successful",
     * )
     *
     * @param Record|null $record
     * @return JsonResponse
     */
    public function deleteAction(Record $record = null): JsonResponse
    {
        if (null === $record) {
            throw new NotFoundHttpException(self::MESSAGE_NOT_FOUND);
        }

        $this->recordService->delete($record);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(methods={"GET"}, path="/{id<\d+>}")
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Returns an existing record",
     *     @Model(type=Record::class, groups={"getRecord"})
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="Could'n find record with this ID",
     * )
     *
     * @param Record|null $record
     * @return JsonResponse
     */
    public function getAction(Record $record = null): JsonResponse
    {
        if (null === $record) {
            throw new NotFoundHttpException(self::MESSAGE_NOT_FOUND);
        }

        return $this->responseFactory->createJsonResponse($record, ['getRecord']);
    }

    /**
     * @Route(methods={"GET"}, path="")
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     required=false,
     *     type="integer"
     * )
     * @SWG\Parameter(
     *     name="offset",
     *     in="query",
     *     required=false,
     *     type="integer"
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Returns all existing records with artists, ordered by artist and title",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Record::class, groups={"getRecord"}))
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBulkAction(Request $request): JsonResponse
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);

        return $this->responseFactory->createJsonResponse(
            $this->recordService->findAll($limit, $offset),
            ['getRecord']
        );
    }
}
