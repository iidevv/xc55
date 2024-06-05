<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * Order page controller
 * 
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    protected function doActionUpdate()
    {
        $order = $this->getOrder();
        
        $qbcIgnore = (Request::getInstance()->qbc_ignore == 'Y') ? 'Y' : 'N';
        $order->setQbcIgnore($qbcIgnore);
        
        parent::doActionUpdate();
    }
}