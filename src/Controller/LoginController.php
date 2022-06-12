<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/api/login", methods={"POST"}, name="app_login")
     * 
     * Supposed to be called only after successful login. It's possible though for $user to be null due to incorrect api call.
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        
        return $this->json([
            'message' => 'Successful login.',
            'token' => $user ? $user->getApiToken() : ''
        ], 200);
    }
}
