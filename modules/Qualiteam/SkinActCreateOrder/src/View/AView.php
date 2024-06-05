<?php
// vim: set ts=4 sw=4 sts=4 et:

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 *
 */
abstract class AView extends \XLite\View\AView
{

    protected function getAddressSectionData(\XLite\Model\Address $address = null, $showEmpty = false)
    {
        $controller = \XLite::getController();

        if (\XLite::getController() instanceof \XLite\Controller\Admin\Order
            && $order = $controller->getOrder()
        ) {
            /** @var \XLite\Model\Order $order */
            if ($order->getManuallyCreated()
                && !$order->getOrigProfile()
            ) {
                return parent::getAddressSectionData($address, true);
            }
        }

        return parent::getAddressSectionData($address, $showEmpty);
    }
}