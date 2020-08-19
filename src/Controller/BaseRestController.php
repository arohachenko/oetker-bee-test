<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Factory\JsonResponseFactory;
use App\Factory\RequestFactory;
use App\Request\RequestDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRestController
{
    protected const MESSAGE_NOT_FOUND = 'Entity not found';

    protected RequestFactory $requestFactory;

    protected JsonResponseFactory $responseFactory;

    protected ValidatorInterface $validator;

    protected function validateJsonBody(Request $httpRequest): void
    {
        $violations = $this->validator->validate($httpRequest->getContent(), [new Assert\Json()]);
        $this->throwOnValidationError($violations);
    }

    protected function validateRequestDTO(RequestDTO $request, ?array $groups = null): void
    {
        $violations = $this->validator->validate($request, null, $groups);
        $this->throwOnValidationError($violations);
    }

    protected function throwOnValidationError(ConstraintViolationListInterface $violations): void
    {
        if (0 !== count($violations)) {
            throw new ValidationException($violations);
        }
    }
}
