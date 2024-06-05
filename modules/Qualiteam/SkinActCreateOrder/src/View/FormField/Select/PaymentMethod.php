<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\FormField\Select;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class PaymentMethod extends \XLite\View\FormField\Select\PaymentMethod
{

    protected function getDefaultOptions()
    {
        if ($this->getOrder()
            && $this->getOrder()->getManuallyCreated()
        ) {
            $list = [];

            $methods = \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->findAllActive();

            foreach ($methods as $method) {
                if ($method->getType() === \XLite\Model\Payment\Method::TYPE_OFFLINE) {
                    $list[$method->getMethodId()] = $method->getTitle();
                }
            }
            return $list;
        }

        return parent::getDefaultOptions();
    }
}