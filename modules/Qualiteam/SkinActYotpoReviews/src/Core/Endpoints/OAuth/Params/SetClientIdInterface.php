<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth\Params;

interface SetClientIdInterface
{
    public const PARAM_CLIENT_ID = "client_id";

    /**
     * @return void
     */
    public function setClientId(): void;
}
