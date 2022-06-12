<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


// Class children of this class must define properties and validations for the request parameters.
// An alternative for such validation could be https://symfony.com/doc/5.3/validation.html.
abstract class BaseRequest
{
    protected array $errors;

    public function __construct(array $requestParams, ValidatorInterface $validator)
    {
        // Populate fields with request properties
        foreach ($requestParams as $propertySnakeCase => $value) {

            // Convert property's name from snake_case to camelCase:
            $propertyCamelCase = lcfirst(str_replace('_', '', ucwords($propertySnakeCase, '_')));
            
            // So only defined properties are validated.
            if (property_exists($this, $propertyCamelCase)) {
                $this->{$propertyCamelCase} = $value;
            }
        }

        // Validate the populated parameters and store errors:
        $result = [];

        /** @var \Symfony\Component\Validator\ConstraintViolation  */
        foreach ($validator->validate($this) as $errMessage) {
    
            $property = $errMessage->getPropertyPath();

            $result[] = [
                'property' => $property,
                'value' => $errMessage->getInvalidValue(),
                'message' => 'Invalid value for ' . $property . '. ' . $errMessage->getMessage()
            ];
        }

        $this->errors = $result;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    // Make sure this method is used only if hasErrors() is true.
    public function getFirstErrorMessage(): string
    {
        return $this->errors[0]['message'];
    }
}