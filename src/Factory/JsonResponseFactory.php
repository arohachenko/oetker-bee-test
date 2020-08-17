<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;
use UnexpectedValueException;

class JsonResponseFactory
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Throwable $exception
     * @return JsonResponse
     */
    public function createErrorResponse(Throwable $exception): JsonResponse
    {
        switch (true) {
            case $exception instanceof HttpException:
                $statusCode = $exception->getStatusCode();
                break;
            case $exception instanceof UnexpectedValueException:
                $statusCode = JsonResponse::HTTP_BAD_REQUEST;
                break;
            default:
                $statusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($this->serializer->serialize($exception, 'json', [
            'statusCode' => $statusCode,
        ]), $statusCode, [], true);
    }
}
