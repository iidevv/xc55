<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Zone extends \XLite\Model\Zone
{
    /**
     * Set zone_id
     *
     * @param integer $zoneId
     */
    public function setZoneId($zoneId)
    {
        $this->zone_id = $zoneId;
    }
}
