<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Security;

use XLite\Model\Profile;

interface UserRepositoryInterface
{
    public function loadUserByIdentifier(string $identifier): ?Profile;

    public function upgradePassword(Profile $user, string $newHashedPassword): void;
}
