<?php

namespace App\Service;

use App\Entity\User;
use \GuzzleHttp\Exception\RequestException; 

class ServiceResponse
{
    public static function processException(RequestException $ex): array
    {
        $response = $ex->getResponse();
        $statusCode = $response->getStatusCode();

        return [
            'message' => json_decode($response->getBody())->message ?? 'Service is not available.',
            'status' => $statusCode === 500 ? 503 : $statusCode // 503 means that the service was not available.
        ];
    }
} 