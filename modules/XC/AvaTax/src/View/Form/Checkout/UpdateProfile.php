<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\View\Form\Checkout;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout update profile form
 * @Extender\Mixin
 */
class UpdateProfile extends \XLite\View\Form\Checkout\UpdateProfile
{
    /**
     * Get validator
     *
     * @return \XLite\Core\Validator\HashArray
     */
    protected function getValidator()
    {
        $types = [
            'A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'N', 'P', 'Q',
            'R', 'MED1',   'MED2',
        ];

        $validator = parent::getValidator();

        $address = $validator->getChild('shippingAddress');
        if ($address) {
            $address->addPair(
                'avaTaxExemptionNumber',
                new \XLite\Core\Validator\TypeString(false),
                \XLite\Core\Validator\Pair\APair::SOFT
            );
            $address->addPair(
                'avaTaxCustomerUsageType',
                new \XLite\Core\Validator\Enum($types),
                \XLite\Core\Validator\Pair\APair::SOFT
            );
        }

        $address = $validator->getChild('billingAddress');
        if ($address) {
            $address->addPair(
                'avaTaxExemptionNumber',
                new \XLite\Core\Validator\TypeString(false),
                \XLite\Core\Validator\Pair\APair::SOFT
            );
            $address->addPair(
                'avaTaxCustomerUsageType',
                new \XLite\Core\Validator\Enum($types),
                \XLite\Core\Validator\Pair\APair::SOFT
            );
        }

        return $validator;
    }
}
