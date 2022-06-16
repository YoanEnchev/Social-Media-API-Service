<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Request\RegisterRequest;
use App\Entity\User;
use App\Service\RequestParamsGenerator;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ConnectException;

class RegisterController extends AbstractController
{
    /**
     * @Route("/api/register", methods={"POST"})
     */
    public function register(Request $request, RequestParamsGenerator $requestParamsGenerator, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $params = $request->request->all();
        $registerRequest = new RegisterRequest($params, $validator);

        if($registerRequest->hasErrors()) {

            return $this->json([
                'message' => $registerRequest->getFirstErrorMessage()
            ], 400);
        }

        $user = new User($params);

        // Hash the password (based on the security.yaml config for the User class).
        $user->setPassword($passwordHasher->hashPassword(
            $user,
            $params['password']
        ));

        $errors = $validator->validate($user);

        // Before inserting the user check if any validations fail about the entity itself (such as unique email).
        if(count($errors) > 0) {

            return $this->json([
                'message' => $errors[0]->getMessage()
            ], 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        try {
            $client = new Client();
            $client->request(
                'POST', $this->getParameter('app.notificationServiceBaseUrl') . 'api/notifications', [
                    'form_params' => [
                        'type' => 'user_registration',
                        'user_id' => $user->getId()
                    ],
                    'headers' => $requestParamsGenerator->getBearerHeaderArray(),
                    'timeout' => 1 // Guzzle does not support "fire and forget" asynchronous requests so we use timeout to avoid waiting for response.
                ]
            );
        } catch(ConnectException $e) {}
    
        return $this->json([
            'message' => 'Successful registration.',
            'token' => $user->getApiToken(),
        ], 200);
    }
}
