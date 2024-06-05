<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params;

interface SetCurrencyInterface
{
    public const PARAM_CURRENCY = "currency";

    /**
     * @return void
     */
    public function setCurrency(): void;
}