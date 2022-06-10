<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Requests\RegisterRequest;

class UserController extends AbstractController
{
 
    /**
     * @Route("/api/register", methods={"POST"})
     */
    public function register(Request $request): JsonResponse
    {
        $params = $request->request->all();
        var_dump([$this->get('validator')]);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
        return 'xx';
        return $this->json([$params]);

        $request->validate();
        
        $username = $params['username'];
        $password = $params['password'];

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
