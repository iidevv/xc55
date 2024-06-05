<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service\Auth;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service;

class ProfileIsLocked extends Service\AuthException
{
    public function __construct()
    {
        parent::__construct('Profile is locked, please contact administrator.');
    }
}
