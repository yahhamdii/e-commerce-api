<?php

namespace Sogedial\ApiBundle\Exception;

class UnauthorizedException extends Exception
{

    protected $message = "The request requires user authentication.";

    const STATUS_CODE = 403;

    public function __construct()
    {

        parent::__construct($this->message);

    }

}
