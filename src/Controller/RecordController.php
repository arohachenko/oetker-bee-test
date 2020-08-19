<?php

namespace App\Controller;

use App\Entity\Record;
use App\Factory\JsonResponseFactory;
use App\Factory\RequestFactory;
use App\Request\SaveRecordRequest;
use App\Service\RecordService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/records")
 * @SWG\Tag(name="Records")
 */
class RecordController extends BaseRestController
{
    protected const MESSAGE_NOT_FOUND = 'Record not found';

    private RecordService $recordService;

    public function __construct(
        RecordService $recordService,
        RequestFactory $requestFactory,
        JsonResponseFactory $responseFactory,
        ValidatorInterface $validator
    ) {
        $this->recordService = $recordService;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->validator = $validator;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(methods={"DELETE"}, path="/{id<\d+>}")
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NO_CONTENT,
     *     description="Deletion successful",
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="Could'n find record with this ID",
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
     *     description="Could'nt find record with this ID",
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
     * @SWG\Parameter(
     *     name="artist",
     *     in="query",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="title",
     *     in="query",
     *     required=false,
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="year",
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
     * @SWG\Response(
     *     response=JsonResponse::HTTP_BAD_REQUEST,
     *     description="Invalid query received",
     * )
     *
     * @param Request $httpRequest
     * @return JsonResponse
     */
    public function getBulkAction(Request $httpRequest): JsonResponse
    {
        $request = $this->requestFactory->createGenericFilterRequest($httpRequest, 20, 0);

        $this->validateRequestDTO($request, ['getRecord']);

        return $this->responseFactory->createJsonResponse(
            $this->recordService->findAll($request),
            ['getRecord']
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(methods={"PUT"}, path="/{id<\d+>}")
     * @SWG\Parameter(
     *     name="record",
     *     in="body",
     *     required=true,
     *     @Model(type=Record::class, groups={"putRecord", "putArtist"})
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Record updated successfully",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Record::class, groups={"getRecord"}))
     *     )
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="Record not found",
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_BAD_REQUEST,
     *     description="Invalid request body",
     * )
     *
     * @param Request $httpRequest
     * @param Record|null $record
     * @return JsonResponse
     */
    public function putAction(Request $httpRequest, ?Record $record = null): JsonResponse
    {
        if (null === $record) {
            throw new NotFoundHttpException(self::MESSAGE_NOT_FOUND);
        }

        $this->validateJsonBody($httpRequest);

        /** @var SaveRecordRequest $request */
        $request = $this->requestFactory->createFromJsonBody($httpRequest, SaveRecordRequest::class);

        $this->validateRequestDTO($request, ['putRecord', 'putArtist']);

        return $this->responseFactory->createJsonResponse(
            $this->recordService->update($record, $request),
            ['getRecord']
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(methods={"POST"}, path="")
     * @SWG\Parameter(
     *     name="record",
     *     in="body",
     *     required=true,
     *     @Model(type=Record::class, groups={"postRecord", "postArtist"})
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_CREATED,
     *     description="Record created successfully",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Record::class, groups={"getRecord"}))
     *     )
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_BAD_REQUEST,
     *     description="Invalid request body",
     * )
     *
     * @param Request $httpRequest
     * @return JsonResponse
     */
    public function postAction(Request $httpRequest): JsonResponse
    {
        $this->validateJsonBody($httpRequest);

        /** @var SaveRecordRequest $request */
        $request = $this->requestFactory->createFromJsonBody($httpRequest, SaveRecordRequest::class);

        $this->validateRequestDTO($request, ['postRecord', 'postArtist']);

        return $this->responseFactory->createJsonResponse(
            $this->recordService->create($request),
            ['getRecord'],
            JsonResponse::HTTP_CREATED
        );
    }
}
