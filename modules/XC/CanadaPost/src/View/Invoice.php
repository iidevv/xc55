<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View;

use XCart\Extender\Mapping\Extender;

/**
 * Invoice widget
 * @Extender\Mixin
 */
abstract class Invoice extends \XLite\View\Invoice
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/xcart.popup.js';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/CanadaPost/js/tracking_controller.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CanadaPost/order/invoice/parts/bottom.d2po.css';

        return $list;
    }

    /**
     * Get selected Canada Post post office
     *
     * @return \XC\CanadaPost\Model\Order\PostOffice|null
     */
    protected function getCapostOffice()
    {
        return $this->getOrder()->getCapostOffice();
    }
}
