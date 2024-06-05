<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product controller
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Customer\Product
{
    /**
     * @inheritdoc
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (!\XLite\Core\Request::getInstance()->isAJAX() && $this->getTarget() == 'product' && $this->getProduct()) {
            \QSL\Segment\Core\Mediator::getInstance()->doViewProduct($this->getProduct());
        }
    }
}
