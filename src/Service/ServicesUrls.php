<?php

namespace App\Service;

class ServicesUrls
{
    private $notificationsBaseUrl;

    public function __construct(string $notificationsBaseUrl)
    {
        $this->notificationsBaseUrl = $notificationsBaseUrl;
    }

    public function getNotificationBaseUrl() :string
    {
        return $this->notificationsBaseUrl;
    }
} 