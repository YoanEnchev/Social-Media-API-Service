<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\TokenAuthenticatedController;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;


class FollowerController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/api/follow/{userId}", methods={"POST"})
     */
    public function sendFollowInvitation(Request $request, int $userId): JsonResponse
    {   
        $followerID = $request->attributes->get('api_token_user')->getId();
        $userToFollow = $doctrine->getRepository(User::class)->find($userId);

        if($userToFollow === null || $userToFollow->getId() === $followerID) {

            return $this->json([
                'message' => 'Invalid user to follow.',
            ], 400);
        }

        // TODO: Send request to notification service for
        // creating invitation notification if such doesn't exist already.

        return $this->json([
            'message' => 'Sent invitation.',
        ], 200);
    }

    /**
     * @Route("/api/follow/{userId}/accept", methods={"POST"})
     */
    public function acceptFollowInvitation(Request $request, ManagerRegistry $doctrine, int $userId): JsonResponse
    {   
        $userToFollow = $request->attributes->get('api_token_user');
        $follower = $doctrine->getRepository(User::class)->find($userId);

        // TODO: Send request to notification service to
        // validate if such invitation exists

        $userToFollow->addFollower($follower);

        $manager = $doctrine->getManager();
        $manager->persist($userToFollow);
        $manager->flush();

        return $this->json([
            'message' => 'Invitation accepted.',
        ], 200);
    }

    /**
     * @Route("/api/follow/{userId}/reject", methods={"POST"})
     */
    public function declineInvitation(Request $request, int $userId): JsonResponse
    {   
        $userToFollow = $request->attributes->get('api_token_user');
        $follower = $doctrine->getRepository(User::class)->find($userId);

        // TODO: Send request to notification service for invitation decline.

        return $this->json([
            'message' => 'Declined invitation.',
        ], 200);
    }

    /**
     * @Route("/api/follow/{userId}", methods={"DELETE"})
     */
    public function unfollowUser(Request $request, ManagerRegistry $doctrine, int $userId): JsonResponse
    {
        $follower = $request->attributes->get('api_token_user');
        $userToUnfollow = $doctrine->getRepository(User::class)->find($userId);

        if($userToUnfollow->getFollowers()->contains($follower)) {
            
            $userToUnfollow->removeFollower($follower);

            return $this->json([
                'message' => 'Unfollowed user.',
            ], 200);
        }

        return $this->json([
            'message' => 'Cannot unfollow user that is not followed.',
        ], 400);
    }
}
