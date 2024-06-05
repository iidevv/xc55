<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Category controller
 * @Extender\Mixin
 */
class Category extends \XLite\Controller\Customer\Category
{
    /**
     * @inheritdoc
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (
            !\XLite\Core\Request::getInstance()->isAJAX()
            && $this->getTarget() == 'category'
            && $this->getCategory()
            && !($this instanceof \XLite\Controller\Customer\Main)
        ) {
            \QSL\Segment\Core\Mediator::getInstance()->doViewCategory($this->getCategory());
        }
    }
}
