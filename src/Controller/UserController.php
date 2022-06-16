<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Controller\TokenAuthenticatedController;

class UserController extends AbstractController implements TokenAuthenticatedController
{
 
    /**
     * @Route("/api/search/{term}", methods={"POST"})
     */
    public function search(string $term, ManagerRegistry $doctrine): JsonResponse
    {
        return $this->json($doctrine->getRepository(User::class)->searchByNames($term), 200);
    }
}
