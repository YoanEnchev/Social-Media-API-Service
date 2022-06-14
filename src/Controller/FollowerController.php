<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\TokenAuthenticatedController;
use App\Entity\User;
use App\Helper\RequestParamsGenerator;
use App\Helper\ServiceResponse;
use Doctrine\Persistence\ManagerRegistry;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException; 

class FollowerController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/api/follow/{userId}", methods={"POST"})
     */
    public function sendFollowInvitation(Request $request, ManagerRegistry $doctrine, int $userId): JsonResponse
    {   
        $follower = $request->attributes->get('api_token_user');
        $userToFollow = $doctrine->getRepository(User::class)->find($userId);

        if($userToFollow === null || $userToFollow->getId() === $follower->getId() || $userToFollow->getFollowers()->contains($follower)) {

            return $this->json([
                'message' => 'Invalid user to follow.',
            ], 400);
        }
        
        try {
            // Send request to notification service for creating invitation notification.
            $client = new Client();
            $client->request(
                'POST', $this->getParameter('app.notificationServiceBaseUrl') . 'api/notification/follow',
                RequestParamsGenerator::generateNotificationRequest('follow_request', $follower, $userToFollow, $this->getParameter('app.notificationMicroserviceSecret'))
            );
        } 
        catch(RequestException $ex) {
            
            $processed = ServiceResponse::processException($ex);

            return $this->json([
                'message' => $processed['message']
            ], $processed['status']);
        }

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

        try {
            // Send request to notification service for accepting follow invitation.
            $client = new Client();
            $client->request(
                'POST', $this->getParameter('app.notificationServiceBaseUrl') . 'api/notification/follow',
                RequestParamsGenerator::generateNotificationRequest('accept_follow_request', $follower, $userToFollow, $this->getParameter('app.notificationMicroserviceSecret'))
            );
        } 
        catch(RequestException $ex) {
            
            $processed = ServiceResponse::processException($ex);

            return $this->json([
                'message' => $processed['message']
            ], $processed['status']);
        }

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
    public function declineInvitation(Request $request, ManagerRegistry $doctrine, int $userId): JsonResponse
    {   
        $userToFollow = $request->attributes->get('api_token_user');
        $follower = $doctrine->getRepository(User::class)->find($userId);

        try {
            // Send request to notification service for accepting follow invitation.
            $client = new Client();
            $client->request(
                'POST', $this->getParameter('app.notificationServiceBaseUrl') . 'api/notification/follow',
                RequestParamsGenerator::generateNotificationRequest('decline_follow_request', $follower, $userToFollow, $this->getParameter('app.notificationMicroserviceSecret'))
            );
        } 
        catch(RequestException $ex) {
            
            $processed = ServiceResponse::processException($ex);

            return $this->json([
                'message' => $processed['message']
            ], $processed['status']);
        }

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
