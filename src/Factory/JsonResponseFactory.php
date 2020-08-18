<?php

namespace App\Factory;

use App\Exception\ValidationException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class JsonResponseFactory implements LoggerAwareInterface
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createJsonResponse($data, ?array $groups = null, ?int $statusCode = null): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($data, 'json', ['groups' => $groups]),
            $statusCode ?? JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @param Throwable $exception
     * @return JsonResponse
     */
    public function createErrorResponse(Throwable $exception): JsonResponse
    {
        $response = $this->serializer->serialize(['message' => $exception->getMessage()], 'json');

        if ($exception instanceof ValidationException) {
            $statusCode = $exception->getStatusCode();
            $response = $this->serializer->serialize($exception->getList(), 'json');
        } elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        $this->logger->warning(sprintf('%s: %s', get_class($exception), $exception->getMessage()));

        return new JsonResponse($response, $statusCode, [], true);
    }
}
