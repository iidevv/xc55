<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Security\Http\Authenticator;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Translation\TranslatorInterface;
use XCart\Security\Http\Authenticator\TokenRepository\TokenRepositoryInterface;

final class TokenAuthenticator extends AbstractAuthenticator
{
    public const HEADER_NAME = 'X-AUTH-TOKEN';

    private TokenRepositoryInterface $tokenRepository;

    private TranslatorInterface $translator;

    public function __construct(
        TokenRepositoryInterface $tokenRepository,
        TranslatorInterface $translator
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->translator      = $translator;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::HEADER_NAME);
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get(self::HEADER_NAME);
        if ($apiToken === null) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport(
            new UserBadge(
                $apiToken,
                function (string $userIdentifier): UserInterface {
                    $user = $this->tokenRepository->getUserByToken($userIdentifier);
                    if ($user === null) {
                        $e = new UserNotFoundException(sprintf('API token "%s" not found.', $userIdentifier));
                        $e->setUserIdentifier($userIdentifier);

                        throw $e;
                    }

                    return $user;
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
