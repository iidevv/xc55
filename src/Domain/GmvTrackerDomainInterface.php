<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Domain;

use XLite\Model\Order;

interface GmvTrackerDomainInterface
{
    public function saveOrderGmvData(array $orderGmvData): void;

    public function prepareOrderGmvData(Order $order): array;
}
