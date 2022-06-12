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

class RegisterController extends AbstractController
{
    /**
     * @Route("/api/register", methods={"POST"}, name="app_register")
     */
    public function register(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
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

        return $this->json([
            'message' => 'Successful registration.',
            'token' => $user->getApiToken()
        ], 200);
    }
}
