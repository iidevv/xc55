<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params;

interface SetPaymentMethodInterface
{
    public const PARAM_PAYMENT_METHOD = "payment_method";

    /**
     * @return void
     */
    public function setPaymentMethod(): void;
}