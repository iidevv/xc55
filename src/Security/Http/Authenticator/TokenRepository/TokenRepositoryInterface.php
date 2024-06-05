<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Security\Http\Authenticator\TokenRepository;

use Symfony\Component\Security\Core\User\UserInterface;

interface TokenRepositoryInterface
{
    public function getUserByToken(string $token): ?UserInterface;
}
