<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Factory\JsonResponseFactory;
use App\Service\ArtistService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/artists")
 * @SWG\Tag(name="Artists")
 */
class ArtistController
{
    private const MESSAGE_NOT_FOUND = 'Artist not found';

    private ArtistService $artistService;

    private JsonResponseFactory $responseFactory;

    public function __construct(ArtistService $artistService, JsonResponseFactory $responseFactory)
    {
        $this->artistService = $artistService;
        $this->responseFactory = $responseFactory;
    }

    /**
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBulkAction(Request $request): JsonResponse
    {
        $limit = $request->query->get('limit', 5);
        $offset = $request->query->get('offset', 0);

        return $this->responseFactory->createJsonResponse(
            $this->artistService->findAll($limit, $offset),
            ['getArtist']
        );
    }
}
