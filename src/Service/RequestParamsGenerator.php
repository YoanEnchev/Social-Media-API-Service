<?php

namespace App\Service;

use App\Entity\User;

class RequestParamsGenerator
{
    private $notificationMicroserviceSecret;

    public function __construct(string $notificationMicroserviceSecret)
    {
        $this->notificationMicroserviceSecret = $notificationMicroserviceSecret;
    }

    public function generateFollowRequest(string $type, User $fromUser, User $toUser): array
    {
        return [
            'form_params' => [
                'action_type' => $type,
                'follower' => [
                    'id' => $fromUser->getId(),
                    'full_name' => $fromUser->getFullName()
                ],
                'followed' => [
                    'id' => $toUser->getId(),
                    'full_name' => $toUser->getFullName()
                ],
            ],
            'headers' => $this->getBearerHeaderArray()
        ];
    }

    public function getBearerHeaderArray(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->notificationMicroserviceSecret
        ];
    }
} 