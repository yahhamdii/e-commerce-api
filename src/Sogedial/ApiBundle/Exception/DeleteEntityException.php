<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author nidhal
 */
class DeleteEntityException extends Exception {

    protected $message="Can\'t remove entity";

    const STATUS_CODE = 500;


    public function __construct(string $message = '') {
        if($message=='')$message= $this->message;

        parent::__construct($message, self::STATUS_CODE);

    }

}
