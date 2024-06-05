<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\View\Admin;

use Qualiteam\SkinActShipStationAdvanced\Traits\ShipstationAdvancedTrait;

class ShipstationStatuses extends \XLite\View\AView
{
    use ShipstationAdvancedTrait;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = static::getStatusesConfigName();

        return $result;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/admin/statuses.twig';
    }
}
