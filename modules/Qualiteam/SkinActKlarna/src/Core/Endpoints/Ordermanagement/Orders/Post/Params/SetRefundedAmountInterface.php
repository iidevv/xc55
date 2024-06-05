<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Ordermanagement\Orders\Post\Params;

interface SetRefundedAmountInterface
{
    const PARAM_REFUNDED_AMOUNT = 'refunded_amount';

    /**
     * @return void
     */
    public function setRefundedAmount(): void;
}