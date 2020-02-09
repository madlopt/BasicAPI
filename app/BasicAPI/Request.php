<?php

namespace BasicAPI;

class Request
{
    public function getParsedBody()
    {
        $body = $this->getBody();
        if ($this->validateJSON($body)) {
            return json_decode($body, true);
        } else {
            return false;
        }
    }

    public function getBody()
    {
        return file_get_contents('php://input');
    }

    private function validateJSON($json_data)
    {
        if (!empty($json_data)) {
            json_decode($json_data);
            return (json_last_error() === JSON_ERROR_NONE);
        } else {
            return false;
        }
    }
}