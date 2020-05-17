<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author elodie
 */
class CartEmptyException extends Exception {

    protected $message="Cart is empty";

    const STATUS_CODE = 404;

    public function __construct(string $message = '') {
        if($message=='')
            $message= $this->message;
        parent::__construct($message, self::STATUS_CODE);

    }

}
