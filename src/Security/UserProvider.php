<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use XCart\Security\DTO\User;
use XLite\Model\Profile;

final class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $profileRepository
    ) {
        $this->repository = $profileRepository;
    }

    public function refreshUser(UserInterface $user): ?UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $profile = $this->repository->loadUserByIdentifier($user->getUserIdentifier());
        if (!$profile) {
            return null;
        }

        return $this->wrapProfile($profile);
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $profile = $this->repository->loadUserByIdentifier($identifier);
        if ($profile === null) {
            $e = new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
            $e->setUserIdentifier($identifier);

            throw $e;
        }

        return $this->wrapProfile($profile);
    }

    public function loadUserByUsername(string $username): ?UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function upgradePassword(User $user, string $newHashedPassword): void
    {
        $this->repository->upgradePassword($user->getWrappedProfile(), $newHashedPassword);
    }

    private function wrapProfile(?Profile $profile): UserInterface
    {
        return new User($profile);
    }
}
