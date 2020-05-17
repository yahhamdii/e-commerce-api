<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author nidhal
 */
class UpdateEntityException extends Exception {

    protected $message="Can\'t update entity";

    const STATUS_CODE = 503;


    public function __construct(string $message = '') {
        if($message=='')$message= $this->message;

        parent::__construct($message, self::STATUS_CODE);

    }

}
