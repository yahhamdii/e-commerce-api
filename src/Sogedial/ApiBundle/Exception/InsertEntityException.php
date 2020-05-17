<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author nidhal
 */
class InsertEntityException extends Exception {

    protected $message="Can\'t insert entity";

    const STATUS_CODE = 501;


    public function __construct(string $message = '') {
        if($message=='')$message= $this->message;

        parent::__construct($message, self::STATUS_CODE);

    }

}
