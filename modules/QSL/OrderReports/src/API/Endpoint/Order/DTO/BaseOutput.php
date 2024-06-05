<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\API\Endpoint\Order\DTO;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class BaseOutput extends \XLite\API\Endpoint\Order\DTO\BaseOutput
{
    /**
     * @var bool
     */
    public bool $mobile_order;
}
