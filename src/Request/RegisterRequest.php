<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use App\Request\BaseRequest;

class RegisterRequest extends BaseRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Email
     */
    protected $email;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(min = 2, max = 255)
     */
    protected $password;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(min = 2, max = 255)
     */
    protected $firstName;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(min = 2, max = 255)
     */
    protected $lastName;
}