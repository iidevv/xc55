<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Message;

class AftershipErrorWorkToDoDataContainer
{
    public function __construct(private $orderTrackingId)
    {
    }

    public function getOrderTrackingId()
    {
        return $this->orderTrackingId;
    }
}