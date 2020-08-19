<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthController
{
    /**
     * @Route(methods={"GET"}, path="/login_check/{username}")
     * @SWG\Parameter(
     *     name="username",
     *     in="path",
     *     required=true,
     *     type="string",
     *     enum={"admin", "user"}
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_OK,
     *     description="Returns auth token"
     * )
     * @SWG\Response(
     *     response=JsonResponse::HTTP_NOT_FOUND,
     *     description="User not found",
     * )
     * @SWG\Tag(name="Auth")
     *
     * @param User $user
     * @param JWTTokenManagerInterface $tokenManager
     * @return JsonResponse
     */
    public function getTokenAction(User $user, JWTTokenManagerInterface $tokenManager): JsonResponse
    {
        return new JsonResponse($tokenManager->create($user));
    }
}
