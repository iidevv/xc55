<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Checkout;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\ListChild;

/**
 * Loyalty earned points widget
 *
 * @ListChild (list="checkout.review.selected.items", weight="60")
 * @ListChild (list="checkout.review.inactive.items", weight="60")
 */
class EarnedPoints extends \XLite\View\AView
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/LoyaltyProgram/checkout/steps/review/parts/items.earned_points.twig';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isFastlaneEnabled();
    }

    /**
     * @return bool
     */
    protected function isFastlaneEnabled()
    {
        return Manager::getRegistry()->isModuleEnabled('XC-FastLaneCheckout')
            && \XC\FastLaneCheckout\Main::isFastlaneEnabled();
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/LoyaltyProgram/checkout/steps/review/parts/items.earned_points.less';

        return $list;
    }
}
