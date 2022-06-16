<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Request\ChatMessageCreationRequest;
use App\Entity\User;
use App\Service\RequestParamsGenerator;
use App\Service\ServiceResponse;
use App\Controller\TokenAuthenticatedController;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;

class NotificationController extends AbstractController implements TokenAuthenticatedController
{
    /**
     * @Route("/api/notifications", methods={"GET"})
     * 
     * Get unseen notifications.
     */
    public function index(Request $request, RequestParamsGenerator $requestParamsGenerator): JsonResponse
    {
        $user = $request->attributes->get('api_token_user');
        $notifications = [];

        try {
            // Extract notifications from server .
            $client = new Client();
            $notifications = $client->request(
                'GET', $this->getParameter('app.notificationServiceBaseUrl') . 'api/notifications?user_id=' . $user->getId(), [
                    'headers' => $requestParamsGenerator->getBearerHeaderArray()
                ]
            )
            ->getBody()
            ->getContents();
        } 
        catch(RequestException $ex) {
            
            $processed = ServiceResponse::processException($ex);

            return $this->json([
                'message' => $processed['message']
            ], $processed['status']);
        }

        return $this->json(json_decode($notifications), 200);
    }

    /**
     * @Route("/api/notifications", methods={"POST"})
     * 
     * Create chat message.
     */
    public function createChatMessage(Request $request, RequestParamsGenerator $requestParamsGenerator, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $params = $request->request->all();
        $chatMessageCreationRequest = new ChatMessageCreationRequest($params, $validator);

        if($chatMessageCreationRequest->hasErrors()) {

            return $this->json([
                'message' => $chatMessageCreationRequest->getFirstErrorMessage()
            ], 400);
        }


        $sender = $request->attributes->get('api_token_user');
        $receiverId = (int) $params['receiver_id'];

        if($entityManager->getRepository(User::class)->find($receiverId) === null) {
            
            return $this->json([
                'message' => 'Receiver does not exist.'
            ], 404);
        }

        try {
            // Create chat notifications.
            $client = new Client();
            $client->request(
                'POST', $this->getParameter('app.notificationServiceBaseUrl') . 'api/notifications', [
                    'form_params' => [
                        'type' => 'chat_message',
                        'sender_id' => $sender->getId(),
                        'receiver_id' => $receiverId,
                        'message_text' => $params['message_text']
                    ],
                    'headers' => $requestParamsGenerator->getBearerHeaderArray()
                ]
            );
        } 
        catch(RequestException $ex) {
            
            $processed = ServiceResponse::processException($ex);

            return $this->json([
                'message' => $processed['message']
            ], $processed['status']);
        }

        return $this->json([
            'message' => 'Successful'
        ], 200);
    }

    /**
     * @Route("/api/notifications/{id}", methods={"PATCH"})
     */
    public function markAsRead(Request $request, RequestParamsGenerator $requestParamsGenerator, int $id): JsonResponse
    {
        try {
            // Mark notifications as read.
            $client = new Client();
            $client->request(
                'PATCH', $this->getParameter('app.notificationServiceBaseUrl') . "api/notifications/$id", [
                    'form_params' => [
                        'type' => 'mark_as_read',
                        'user_id' => $request->attributes->get('api_token_user')->getId()
                    ],
                    'headers' => $requestParamsGenerator->getBearerHeaderArray()
                ]
            );
        } 
        catch(RequestException $ex) {
            
            $processed = ServiceResponse::processException($ex);

            return $this->json([
                'message' => $processed['message']
            ], $processed['status']);
        }

        return $this->json([
            'message' => 'Successful'
        ], 200);
    }
}
