<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Helper;

use XLite\Model\OrderTrackingNumber;

interface IOrderTrackingNumberWorkerHelper
{
    public function shouldBeUpdated(OrderTrackingNumber $orderTrackingNumber): bool;

    public function updateOrderTrackingNumber(OrderTrackingNumber $orderTrackingNumber): void;

    public function finishJob(): void;
}