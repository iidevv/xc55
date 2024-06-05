<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth\Params;

interface SetGrantTypeInterface
{
    public const PARAM_GRANT_TYPE = "grant_type";

    /**
     * @return void
     */
    public function setGrantType(): void;
}
