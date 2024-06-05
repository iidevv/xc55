<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\Add2CartPopup")
 */
class Add2CartProducts extends \XC\Add2CartPopup\View\Products
{
    /**
     * Return products list: temporary disable coming-soon products to exclude them from result
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return mixed
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $oldCsEnabled = \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled;

        \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled = false;

        $result = parent::getData($cnd, $countOnly);

        \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled = $oldCsEnabled;

        return $result;
    }
}
