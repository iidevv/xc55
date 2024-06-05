<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Security\Hasher;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use XLite\Core\Auth;

final class PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return Auth::comparePassword($plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return Auth::comparePassword($hashedPassword, $plainPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }
}
