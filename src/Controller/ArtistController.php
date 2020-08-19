<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Factory\JsonResponseFactory;
use App\Factory\RequestFactory;
use App\Request\SaveArtistRequest;
use App\Service\ArtistService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/artists")
 * @SWG\Tag(name="Artists")
 */
class ArtistController extends BaseRestController
{
    protected const MESSAGE_NOT_FOUND = 'Artist not found';

    private ArtistService $artistService;

    public function __construct(
        ArtistService $artistService,
        RequestFactory $requestFactory,
        JsonResponseFactory $responseFactory,
        ValidatorInterface $validator
    ) {
        $this->artistService = $artistService;
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
     *     description="Could'n find artist with this ID",
     * )
     *
     * @param Artist|null $artist
     * @return JsonResponse
     */
    public function deleteAction(Artist $artist = null): JsonResponse
    {
        if (null === $artist) {
            throw new NotFoundHttpException(self::MESSAGE_NOT_FOUND);
        }

        $this->artistService->delete($artist);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Route(methods={"GET"}, path="/{id<\d+>}")
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Returns an existing artist",
     *     @Model(type=Artist::class, groups={"getArtist"})
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="Could'nt find artist with this ID",
     * )
     *
     * @param Artist|null $artist
     * @return JsonResponse
     */
    public function getAction(Artist $artist = null): JsonResponse
    {
        if (null === $artist) {
            throw new NotFoundHttpException(self::MESSAGE_NOT_FOUND);
        }

        return $this->responseFactory->createJsonResponse($artist, ['getArtist']);
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
     *     description="Returns all existing artists with records",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Artist::class, groups={"getArtist"}))
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
        $request = $this->requestFactory->createGenericFilterRequest($httpRequest, 5, 0);

        $this->validateRequestDTO($request, ['getArtist']);

        return $this->responseFactory->createJsonResponse(
            $this->artistService->findAll($request),
            ['getArtist']
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(methods={"PUT"}, path="/{id<\d+>}")
     * @SWG\Parameter(
     *     name="artist",
     *     in="body",
     *     required=true,
     *     @Model(type=Artist::class, groups={"putArtist"})
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Artist updated successfully",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Artist::class, groups={"getArtist"}))
     *     )
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="Artist not found",
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_BAD_REQUEST,
     *     description="Invalid request body",
     * )
     *
     * @param Request $httpRequest
     * @param Artist|null $artist
     * @return JsonResponse
     */
    public function putAction(Request $httpRequest, ?Artist $artist = null): JsonResponse
    {
        if (null === $artist) {
            throw new NotFoundHttpException(self::MESSAGE_NOT_FOUND);
        }

        $this->validateJsonBody($httpRequest);

        /** @var SaveArtistRequest $request */
        $request = $this->requestFactory->createFromJsonBody($httpRequest, SaveArtistRequest::class);

        $this->validateRequestDTO($request, ['putArtist']);

        return $this->responseFactory->createJsonResponse(
            $this->artistService->update($artist, $request),
            ['getArtist']
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route(methods={"POST"}, path="")
     * @SWG\Parameter(
     *     name="artist",
     *     in="body",
     *     required=true,
     *     @Model(type=Artist::class, groups={"postArtist"})
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_CREATED,
     *     description="Artist created successfully",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Artist::class, groups={"getArtist"}))
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

        /** @var SaveArtistRequest $request */
        $request = $this->requestFactory->createFromJsonBody($httpRequest, SaveArtistRequest::class);

        $this->validateRequestDTO($request, ['postArtist']);

        return $this->responseFactory->createJsonResponse(
            $this->artistService->create($request),
            ['getArtist'],
            JsonResponse::HTTP_CREATED
        );
    }
}
