<?php

namespace BasicAPI;

use BasicAPI\Exception\APIException;
use Monolog\Logger;
use NoahBuscher\Macaw\Macaw;

class Router
{
    public Response $response;
    public Database $db;
    public Access $access;

    public function applyRoutingRules(Response $response, Database $db, Access $access)
    {
        $this->response = $response;
        $this->db = $db;
        $this->access = $access;

        try {
            $this->setRoutingRules();
            Macaw::dispatch();
        } catch (APIException $e) {
            $statusCode = $e->getStatusCode();
            $message = $e->getMessage();
            $this->response->showResponse($message, $statusCode);
            /** @var Logger $logger */
            $logger = Registry::get('logger');
            $logger->error(
                json_encode(
                    $e->getMessage() . " Where: " . $e->getFile() . " (" . $e->getLine(
                    ) . "), IP: " . @$_SERVER['REMOTE_ADDR'] . " URI: " . @$_SERVER['REQUEST_URI']
                )
            );
        }
    }

    public function setRoutingRules()
    {
    }

    private function __clone()
    {
    }

}