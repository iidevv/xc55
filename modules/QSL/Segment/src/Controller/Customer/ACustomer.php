<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract customer controller
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * @inheritdoc
     */
    protected function doActionChangeLanguage()
    {
        $old = \XLite\Core\Session::getInstance()->getLanguage();

        parent::doActionChangeLanguage();

        if ($old->getCode() != \XLite\Core\Session::getInstance()->getLanguage()->getCode()) {
            \QSL\Segment\Core\Mediator::getInstance()->doChangeLanguage(
                $old,
                \XLite\Core\Session::getInstance()->getLanguage()
            );
        }
    }
}
