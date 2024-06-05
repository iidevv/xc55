<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\Action\Base\AAction;

/**
 * @Extender\Mixin
 *
 * Main
 */
class QuickLook extends \XLite\View\Product\Details\Customer\Page\QuickLook
{
    /**
     * Get container attributes
     */
    protected function getGAData()
    {
        return $this->getAction()->getActionData(AAction::FORMAT_JSON);
    }

    protected function getAction(): Action\ProductInfo
    {
        return new Action\ProductInfo(
            $this->getProduct()
        );
    }
}
