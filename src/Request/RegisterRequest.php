<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use App\Request\BaseRequest;

class RegisterRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    protected $email;

    #[Type('string')]
    #[NotBlank()]
    protected $password;
}