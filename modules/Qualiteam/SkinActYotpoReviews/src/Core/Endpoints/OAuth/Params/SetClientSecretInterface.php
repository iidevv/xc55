<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth\Params;

interface SetClientSecretInterface
{
    public const PARAM_CLIENT_SECRET = "client_secret";

    /**
     * @return void
     */
    public function setClientSecret(): void;
}
