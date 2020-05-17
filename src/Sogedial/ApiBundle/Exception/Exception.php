<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author nidhal
 */
class Exception extends \Exception implements ExceptionInterface {
    
    private $statusCode;


    public function __construct(string $message = "", int $statusCode = 0, \Throwable $previous = null) {
        $this->statusCode=$statusCode;
        parent::__construct($message, $statusCode, $previous);
    }
    
    public function getStatusCode() {
        return $this->statusCode;
    }


    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
