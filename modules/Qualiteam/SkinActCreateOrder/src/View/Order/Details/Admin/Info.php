<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Order\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\Order\Details\Admin\Info
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActCreateOrder/order_style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->getOrder()
            && $this->getOrder()->getManuallyCreated()
            && !$this->getOrder()->getOrigProfile()
        ) {
            $list[] = 'modules/Qualiteam/SkinActCreateOrder/shipping_recalc_not_frozen.js';
        }

        return $list;
    }

    protected function getRedirectUrl()
    {
        return $this->buildURL('orders', null, ['order_number' => $this->getOrder()->getOrderNumber()]);
    }

    protected function canEditOrderLogin()
    {
        return true;
    }

    protected function getProfileName()
    {
        $name = parent::getProfileName();

        if ($name === ''
            && $this->getOrder()->getManuallyCreated()
            && $this->getOrder()->getOrigProfile()
        ) {
            $profile = $this->getOrder()->getProfile();
            $name = $profile->getName();
        }

        return $name;
    }
}
