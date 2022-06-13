<?php

namespace App\Helper;

use App\Entity\User;

class RequestParamsGenerator
{
    public static function generateNotificationRequest(string $type, User $fromUser, User $toUser, string $bearerToken): array
    {
        return [
            'form_params' => [
                'action_type' => $type,
                'from_user' => [
                    'id' => $fromUser->getId(),
                    'full_name' => $fromUser->getFullName()
                ],
                'to_user' => [
                    'id' => $toUser->getId(),
                    'full_name' => $toUser->getFullName()
                ],
            ],
            'headers' => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ];
    }
} 