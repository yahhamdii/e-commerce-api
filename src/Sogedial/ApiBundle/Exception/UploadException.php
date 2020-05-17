<?php

namespace Sogedial\ApiBundle\Exception;

/**
 *
 * @author elodie
 */
class UploadException extends Exception {

    protected $message="Upload Error";

    const STATUS_CODE = 504;

    public function __construct(string $message = '') {
        if($message !== '')$message = $this->message.' : '.$message;

        parent::__construct($message, self::STATUS_CODE);

    }

}
