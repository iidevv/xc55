<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Module\XC\FastLaneCheckout\View;

use XCart\Extender\Mapping\Extender;

/**
 * Disable default one-page checkout in case of fastlane checkout
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\FastLaneCheckout")
 */
class SectionsNavigation extends \XC\FastLaneCheckout\View\SectionsNavigation
{
    /**
     * Defines the additional data array
     *
     * @return array
     */
    protected function defineWidgetData()
    {
        $data = parent::defineWidgetData();

        if ($this->isReturnedAfterXpc()) {
            $data = array_merge(
                $data,
                array(
                    'start_with' => 'payment'
                )
            );
        }

        return $data;
    }
}
