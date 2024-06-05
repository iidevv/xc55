<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Offline shipping method view model
 * @Extender\Mixin
 */
class Offline extends \XLite\View\Model\Shipping\Offline
{
    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        $entity = $this->getModelObject();

        if ($entity->getFree() || $entity->isFixedFee()) {
            unset(
                $schema['tableType'],
                $schema['shippingZone'],
                $schema['handlingFee'],
                $schema['taxClass']
            );
        }

        return parent::getFieldsBySchema($schema);
    }
}
