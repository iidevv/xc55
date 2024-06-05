<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params;

interface SetOrderLinesInterface
{
    const PARAM_ORDER_LINES       = 'order_lines';

    /**
     * Set order lines
     *
     * @return void
     */
    public function setOrderLines(): void;
}