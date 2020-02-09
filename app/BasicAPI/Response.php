<?php

namespace BasicAPI;

class Response
{
    private const PHRASES = [
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];

    private int $statusCode;
    private string $reasonPhrase;
    private $body;

    public function __construct(
        int $status = 200,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        string $reason = null
    ) {
        if (empty($headers)) {
            $headers = Registry::get('headers');
        }
        $this->setHeaders($headers);
        $this->statusCode = $status;
        $this->body = $body;
        if ($reason == '' && isset(self::PHRASES[$this->statusCode])) {
            $this->reasonPhrase = self::PHRASES[$this->statusCode];
        } else {
            $this->reasonPhrase = (string)$reason;
        }

        $this->protocol = $version;
    }

    private function setHeaders(array $headers = null)
    {
        if (!headers_sent() && !empty($headers)) {
            foreach ($headers as $key => $value) {
                header("$key: $value");
            }
        }
    }

    public function showResponse(string $message = '', int $statusCode = 200, $data = '')
    {
        $array['message'] = $message;
        $array['data'] = $data;

        if (!empty(self::PHRASES[$statusCode])) {
            $this->statusCode = $statusCode;
            $this->reasonPhrase = self::PHRASES[$this->statusCode];
        } else {
            return false;
        }

        $array['status_code'] = $this->statusCode;
        $array['status_message'] = $this->reasonPhrase;

        if (!empty($array['status_code']) && !empty($array['status_message'])) {
            if (!headers_sent()) {
                header(
                    $_SERVER['SERVER_PROTOCOL'] . " " . $array['status_code'] . $array['status_message']
                );
                $this->setHeaders();
                echo json_encode($array, JSON_UNESCAPED_UNICODE);
                Registry::set('response_sent', true);
            }
        } else {
            if (!headers_sent()) {
                header($_SERVER['SERVER_PROTOCOL'] . " 418 I'm a teapot");
                $this->setHeaders();
                echo json_encode($array, JSON_UNESCAPED_UNICODE);
                Registry::set('response_sent', true);
            }
        }
        return true;
    }
}
