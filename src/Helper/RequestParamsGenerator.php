<?php

namespace App\Helper;

use App\Entity\User;

class RequestParamsGenerator
{
    public static function generateWelcomeRequest(User $user, string $bearerToken): array
    {
        return [
            'form_params' => [
                'user_id' => $user->getId()
            ],
            'headers' => self::getBearerHeaderArray($bearerToken)
        ];
    }

    public static function generateFollowRequest(string $type, User $fromUser, User $toUser, string $bearerToken): array
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
            'headers' => self::getBearerHeaderArray($bearerToken)
        ];
    }

    public static function getBearerHeaderArray(string $bearerToken): array
    {
        return [
            'Authorization' => "Bearer $bearerToken"
        ];
    }
} 