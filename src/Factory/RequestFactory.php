<?php

namespace App\Factory;

use App\Request\GenericFilterRequest;
use App\Request\RequestDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class RequestFactory
{
    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createGenericFilterRequest(
        Request $request,
        int $defaultLimit,
        int $defaultOffset
    ): GenericFilterRequest {

        $queryBag = $request->query;

        return new GenericFilterRequest(
            $queryBag->get('limit', $defaultLimit),
            $queryBag->get('offset', $defaultOffset),
            null === $queryBag->get('artist') ? null : trim($queryBag->get('artist')),
            null === $queryBag->get('title') ? null : trim($queryBag->get('title')),
            $queryBag->get('year'),
        );
    }

    /**
     * @param Request $httpRequest
     * @param string $type
     * @return object|RequestDTO
     */
    public function createFromJsonBody(Request $httpRequest, string $type): object
    {
        /** @var object|RequestDTO $request */
        $request = $this->serializer->deserialize(
            $httpRequest->getContent(),
            $type,
            'json'
        );

        return $request;
    }
}
