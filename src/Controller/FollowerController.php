<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FollowerController extends AbstractController
{
    /**
     * @Route("/api/follow/{userId}", methods={"POST"})
     */
    public function sendFollowInvitation(Request $request, int $userId)
    {   


        return $this->json([
            'message' => 'Sent invitation.',
        ], 200);
    }

    /**
     * @Route("/api/follow/{userId}/accept", methods={"POST"})
     */
    public function acceptFollowInvitation(Request $request, int $userId)
    {   
        return $this->json([
            'message' => 'Invitation accepted.',
        ], 200);
    }

    /**
     * @Route("/api/follow/{userId}/reject", methods={"POST"})
     */
    public function declineInvitation(Request $request, int $userId)
    {   
        return $this->json([
            'message' => 'Declined invitation.',
        ], 200);
    }

    /**
     * @Route("/api/follow/{userId}", methods={"DELETE"})
     */
    public function unfollowUser(Request $request, int $userId)
    {

        return $this->json([
            'message' => 'Unfollowed user.',
        ], 200);
    }
}
