<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActAftership\Core\Helper\Factory;

use Qualiteam\SkinActAftership\Core\Helper\WorkerHelperSetAftershipError;
use Qualiteam\SkinActAftership\Core\Helper\IOrderTrackingNumberWorkerHelper;

class OrderTrackingNumberWorkerHelper
{
    public static function getHelper(): IOrderTrackingNumberWorkerHelper
    {
        return new WorkerHelperSetAftershipError();
    }
}