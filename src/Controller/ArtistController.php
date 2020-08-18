<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Service\ArtistService;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/artists")
 * @SWG\Tag(name="Artists")
 */
class ArtistController
{
    private ArtistService $artistService;

    public function __construct(ArtistService $artistService)
    {
        $this->artistService = $artistService;
    }

    /**
     * @Route(methods={"DELETE"}, path="/{id<\d+>}")
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NO_CONTENT,
     *     description="Deletion successful",
     * )
     *
     * @param Artist|null $artist
     * @return JsonResponse
     */
    public function deleteAction(Artist $artist = null): JsonResponse
    {
        if (null === $artist) {
            throw new NotFoundHttpException('Artist not found');
        }

        $this->artistService->delete($artist);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
