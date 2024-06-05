<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Security\DTO;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use XLite\Core\Auth;
use XLite\Model\Profile;

final class User implements UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface, EquatableInterface, PasswordHasherAwareInterface
{
    private ?Profile $profile;

    public function __construct(?Profile $profile)
    {
        $this->profile = $profile;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->profile->getLogin();
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        if (!$this->profile) {
            return [];
        }

        return $this->profile->getAccessLevel() === Auth::getInstance()->getAdminAccessLevel()
            ? ['ROLE_USER', 'ROLE_ADMIN']
            : ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        if (!$this->profile) {
            return null;
        }

        return $this->profile->getPassword();
    }

    public function getSalt(): ?string
    {
        if (!$this->profile) {
            return null;
        }

        return $this->profile->getSalt();
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): ?string
    {
        return $this->getUserIdentifier();
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $this->getUserIdentifier() === $user->getUserIdentifier();
    }

    public function __toString(): string
    {
        return (string)$this->getUserIdentifier();
    }

    public function getWrappedProfile(): Profile
    {
        return $this->profile;
    }

    // PasswordHasherAwareInterface

    public function getPasswordHasherName(): ?string
    {
        return 'xcart';
    }
}
