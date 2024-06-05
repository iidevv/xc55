<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\ItemsList\Model\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping rates list
 * @Extender\Mixin
 */
class Markups extends \XLite\View\ItemsList\Model\Shipping\Markups
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        $entity = $this->getModelForm()->getModelObject();

        return parent::isVisible() && !$entity->getFree() && !$entity->isFixedFee();
    }
}
