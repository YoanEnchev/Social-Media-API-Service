<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class UserController extends AbstractController
{
 
    /**
     * @Route("/api/search/{term}", methods={"POST"})
     */
    public function search(string $term, ManagerRegistry $doctrine): Response
    {
        return $this->json([
            'users' => $doctrine->getRepository(User::class)->searchByNames($term)
        ], 200);
    }
}
