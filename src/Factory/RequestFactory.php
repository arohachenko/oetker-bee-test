<?php

namespace App\Factory;

use App\Request\GenericFilterRequest;
use Symfony\Component\HttpFoundation\Request;

class RequestFactory
{
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
}
