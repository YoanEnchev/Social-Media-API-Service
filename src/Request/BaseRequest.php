<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


// Class children of this class must define properties and validations for the request parameters.
// An alternative for such validation could be https://symfony.com/doc/5.3/validation.html.
abstract class BaseRequest
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        // Populate fields with request properties
        foreach (Request::createFromGlobals()->toArray() as $property => $value) {
            
            // So only defined properties are validated.
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    public function validate(): array
    {
        $result = [];

        /** @var \Symfony\Component\Validator\ConstraintViolation  */
        foreach ($this->validator->validate($this) as $errMessage) {
            $result['errors'][] = [
                'property' => $errMessage->getPropertyPath(),
                'value' => $errMessage->getInvalidValue(),
                'message' => $errMessage->getMessage(),
            ];
        }

        return $result;
    }
}