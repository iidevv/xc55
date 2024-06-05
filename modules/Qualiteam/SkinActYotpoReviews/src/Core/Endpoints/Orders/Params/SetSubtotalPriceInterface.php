<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params;

interface SetSubtotalPriceInterface
{
    public const PARAM_SUBTOTAL_PRICE = "subtotal_price";

    /**
     * @return void
     */
    public function setSubtotalPrice(): void;
}