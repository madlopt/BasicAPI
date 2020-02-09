<?php


namespace BasicAPI\Exception;

use Exception;
use Throwable;

class APIException extends Exception implements APIExceptionInterface
{
    private $statusCode;

    public function __construct($message = "", $code = 0, Throwable $previous = null, $statusCode = 200)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

}
