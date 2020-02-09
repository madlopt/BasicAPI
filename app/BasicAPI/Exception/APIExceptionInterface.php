<?php


namespace BasicAPI\Exception;

use Throwable;

interface APIExceptionInterface extends Throwable
{
    public function getStatusCode();
}