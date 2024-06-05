<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Helper;

use XLite\Core\Database;
use XLite\Model\OrderTrackingNumber;

class WorkerHelperSetAftershipError implements IOrderTrackingNumberWorkerHelper
{

    public function shouldBeUpdated(OrderTrackingNumber $orderTrackingNumber): bool
    {
        return !empty($orderTrackingNumber->getAftershipCourierName());
    }

    public function updateOrderTrackingNumber(OrderTrackingNumber $orderTrackingNumber): void
    {
        $orderTrackingNumber->setShipstationSlugError(false);
    }

    public function finishJob(): void
    {
        Database::getEM()->flush();
    }
}