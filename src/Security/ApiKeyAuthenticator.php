<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        $name = $request->get('_route');

        return ($name !== 'app_login' && $name !== 'app_register' && $name !== 'home');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->request->get('api_token');
        
        // If exception is thrown, onAuthenticationFailure is called.
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        if (!is_string($apiToken)) {
            throw new CustomUserMessageAuthenticationException('API token must be string.');
        }

        $user = $this->doctrine->getRepository(User::class)->findOneByApiToken($apiToken);

        if (null === $user) {
            throw new CustomUserMessageAuthenticationException('No user is matched by the API token.');
        }

        // Set attributes allows for passing data from authenticator to controller.
        // This means that there is no need of additional SQL requests to extract the user.
        $request->attributes->set('user', $user);

        return new SelfValidatingPassport(new UserBadge($apiToken));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}