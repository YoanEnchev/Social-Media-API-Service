<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use App\Request\BaseRequest;

class ChatMessageCreationRequest extends BaseRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("numeric")
     */
    protected $receiverId;

    /** 
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    protected $messageText;
}