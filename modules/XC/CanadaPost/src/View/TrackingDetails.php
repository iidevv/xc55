<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Tracking details page
 *
 * @ListChild (list="center")
 * @ListChild (list="admin.center", zone="admin")
 */
class TrackingDetails extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'capost_tracking';

        return $result;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CanadaPost/tracking_details/body.twig';
    }
}
