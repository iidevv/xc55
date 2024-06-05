<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Inline\Select;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class PaymentMethod extends \XLite\View\FormField\Inline\Select\PaymentMethod
{
    /**
     * @param array $field Field
     *
     * @return string
     */
    protected function getViewValue(array $field)
    {
        $entity = $this->getEntity();
        if ($entity->getPaymentMethod()) {
            if ($entity->getOrder()->isPaypalCommercePlatform($entity->getPaymentMethod())) {
                return $entity->getOrder()->getPaymentMethodName();
            }
        }

        return parent::getViewValue($field);
    }
}
