<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params;

interface SetPaymentStatusInterface
{
    public const PARAM_PAYMENT_STATUS = "payment_status";

    /**
     * @return void
     */
    public function setPaymentStatus(): void;
}