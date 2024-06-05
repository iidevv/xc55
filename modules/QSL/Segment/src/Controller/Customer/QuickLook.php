<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * QuickLook controller
 * @Extender\Mixin
 */
class QuickLook extends \XLite\Controller\Customer\QuickLook
{
    /**
     * @inheritdoc
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->isAJAX() && $this->getTarget() == 'quick_look' && $this->getProduct()) {
            \QSL\Segment\Core\Mediator::getInstance()->doViewProduct($this->getProduct());
        }
    }
}
