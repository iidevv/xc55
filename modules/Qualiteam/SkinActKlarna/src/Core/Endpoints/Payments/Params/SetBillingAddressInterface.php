<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params;

interface SetBillingAddressInterface
{
    const PARAM_BILLING_ADDRESS = 'billing_address';

    /**
     * Set billing address
     *
     * @return void
     */
    public function setBillingAddress(): void;
}