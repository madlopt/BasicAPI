<?php

namespace BasicAPI;

use BasicAPI\Exception\UnauthorizedException;

class Access
{
    public function allowOnlyForAuthorizedUsers(Database $db)
    {
        $token = $this->getBearerToken();
        if (!empty($token)) {
            $tokenData = $db->getTokenData($token);
            if (!empty($tokenData['token_expiration_time'])) {
                if (time() < (int)$tokenData['token_expiration_time']) {
                    return true;
                } else {
                    throw new UnauthorizedException('Your token has expired', 401);
                }
            } else {
                throw new UnauthorizedException('Wrong token', 401);
            }
        } else {
            throw new UnauthorizedException('No token', 401);
        }
    }

    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return false;
    }

    private function getAuthorizationHeader()
    {
        $headers = false;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(
                    array_map('ucwords', array_keys($requestHeaders)),
                    array_values($requestHeaders)
                );
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
        }
        return $headers;
    }
}