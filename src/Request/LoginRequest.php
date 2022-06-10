<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use App\Request\BaseRequest;

class LoginRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    protected $username;

    #[Type('string')]
    #[NotBlank()]
    protected $password;
}