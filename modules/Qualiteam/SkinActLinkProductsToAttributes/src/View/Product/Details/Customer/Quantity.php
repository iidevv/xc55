<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCached;

/**
 * @Extender\Mixin
 */
class Quantity extends \XLite\View\Product\Details\Customer\Quantity
{
    /**
     * Define the CSS classes
     *
     * @return string
     */
    protected function getCSSClass()
    {
        return parent::getCSSClass() . ($this->hasLinkedAttributes() ? ' linked-attributes-defined' : '');
    }

    /**
     * Check if the product has linked attributes
     *
     * @return boolean
     */
    protected function hasLinkedAttributes()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            if ($this->getProduct()->getEditableAttributes()) {
                foreach ($this->getProduct()->getEditableAttributes() as $attr) {
                    if ($attr->getType() === \XLite\Model\Attribute::TYPE_CHECKBOX
                        || $attr->getType() === \XLite\Model\Attribute::TYPE_SELECT) {
                        foreach ($attr->getAttributeValues() as $value) {
                            if ($value->getLinkedProduct()) {
                                return true;
                            }
                        }
                    }
                }
            }
        }, ['hasLinkedAttributes', $this->getProduct()->getProductId()]);
    }
}