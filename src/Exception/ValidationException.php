<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends BadRequestHttpException
{
    private ConstraintViolationListInterface $list;

    /**
     * @param ConstraintViolationListInterface $list
     */
    public function __construct(ConstraintViolationListInterface $list)
    {
        $this->list = $list;

        if ($list instanceof ConstraintViolationList) {
            parent::__construct($list->__toString());
        }
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getList(): ConstraintViolationListInterface
    {
        return $this->list;
    }
}
