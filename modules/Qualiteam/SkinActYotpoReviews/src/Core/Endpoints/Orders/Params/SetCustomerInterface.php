<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params;

interface SetCustomerInterface
{
    public const PARAM_CUSTOMER = "customer";

    /**
     * @return void
     */
    public function setCustomer(): void;
}