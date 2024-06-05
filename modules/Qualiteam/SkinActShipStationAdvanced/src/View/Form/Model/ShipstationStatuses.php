<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\View\Form\Model;

use Qualiteam\SkinActShipStationAdvanced\Traits\ShipstationAdvancedTrait;

class ShipstationStatuses extends \XLite\View\Form\AForm
{
    use ShipstationAdvancedTrait;

    protected function getDefaultTarget()
    {
        return static::getStatusesConfigName();
    }

    protected function getDefaultAction()
    {
        return 'update';
    }
}