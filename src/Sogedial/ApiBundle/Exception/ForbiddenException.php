<?php

namespace Sogedial\ApiBundle\Exception;

class ForbiddenException extends Exception
{

    const STATUS_CODE = 403;

    public function __construct(string $message = '')
    {
        parent::__construct($message, self::STATUS_CODE);
    }

}
