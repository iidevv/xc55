<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params;

interface SetShippingAddressInterface
{
    public const PARAM_SHIPPING_ADDRESS = "shipping_address";

    /**
     * @return void
     */
    public function setShippingAddress(): void;
}