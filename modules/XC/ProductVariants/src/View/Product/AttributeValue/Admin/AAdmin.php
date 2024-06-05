<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\AttributeValue\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract attribute value (admin)
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Product\AttributeValue\Admin\AAdmin
{
    /**
     * Check attribute is modified or not
     *
     * @return boolean
     */
    protected function isModified()
    {
        $result = parent::isModified();

        if ($result && $this->getAttribute() && $this->getProduct()->mustHaveVariants()) {
            foreach ($this->getProduct()->getVariantsAttributes() as $attr) {
                if ($attr->getId() == $this->getAttribute()->getId()) {
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }
}
