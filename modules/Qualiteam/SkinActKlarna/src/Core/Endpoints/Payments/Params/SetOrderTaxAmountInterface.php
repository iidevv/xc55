<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params;

interface SetOrderTaxAmountInterface
{
    const PARAM_ORDER_TAX_AMOUNT = 'order_tax_amount';

    /**
     * Set order tax amount
     *
     * @return void
     */
    public function setOrderTaxAmount(): void;
}