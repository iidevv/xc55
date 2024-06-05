<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Params;

interface SetBillingAddressInterface
{
    public const PARAM_BILLING_ADDRESS = "billing_address";

    /**
     * @return void
     */
    public function setBillingAddress(): void;
}