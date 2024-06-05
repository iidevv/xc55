<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\XC\FastLaneCheckout\Core\GA;

use XCart\Extender\Mapping\Extender;
use XC\FastLaneCheckout;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\FastLaneCheckout")
 */
abstract class AJsList extends \CDev\GoogleAnalytics\Core\GA\AJsList
{
    protected function getCheckoutTrackingList(): array
    {
        if (!$this->needsFastlaneJs()) {
            return parent::getCheckoutTrackingList();
        }

        return $this->getFastlaneCheckoutTrackingList();
    }

    protected function needsFastlaneJs(): bool
    {
        return FastLaneCheckout\Main::isFastlaneEnabled();
    }

    /**
     * @return string[]
     */
    protected function getFastlaneCheckoutTrackingList(): array
    {
        return [
            'modules/CDev/GoogleAnalytics/adapters/adapters/base.js',
            'modules/CDev/GoogleAnalytics/adapters/adapters/fastlane.js',
        ];
    }
}
