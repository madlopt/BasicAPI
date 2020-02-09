<?php


namespace BasicAPI\Exception;

use Throwable;

class ForbiddenException extends APIException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null, $statusCode = 200)
    {
        parent::__construct($message, $code, $previous, 403);
    }
}