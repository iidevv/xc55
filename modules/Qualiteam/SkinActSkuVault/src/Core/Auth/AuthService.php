<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Auth;

/**
 * Interface AuthService
 */
interface AuthService
{
    /**
     * Get middleware
     *
     * @return callable
     */
    public function getMiddleWare(): callable;
}