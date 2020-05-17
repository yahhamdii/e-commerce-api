<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author elodie
 */
class OrderForbiddenException extends Exception {

    protected $message="You are not allowed to view this order";

    const STATUS_CODE = 403;

    public function __construct(string $message = '') {
        if($message=='')
            $message= $this->message;
        parent::__construct($message, self::STATUS_CODE);

    }

}
