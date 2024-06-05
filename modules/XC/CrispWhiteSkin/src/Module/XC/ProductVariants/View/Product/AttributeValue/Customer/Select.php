<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\ProductVariants\View\Product\AttributeValue\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Attribute value (Select)
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
abstract class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{
    /**
     * @return string
     */
    protected function getOptionTemplate()
    {
        if ($this->getProduct()->mustHaveVariants()) {
            return $this->useBlocksMode()
                ? 'modules/XC/ProductVariants/product/attribute_value/select/block.twig'
                : 'modules/XC/ProductVariants/product/attribute_value/select/option.twig';
        }

        return parent::getOptionTemplate();
    }

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate()
    {
        if (
            $this->getProduct()->mustHaveVariants()
            && \XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER
        ) {
            return $this->useBlocksMode()
                ? 'modules/XC/ProductVariants/product/attribute_value/select/blocks.twig'
                : 'modules/XC/ProductVariants/product/attribute_value/select/selectbox.twig';
        }

        return parent::getTemplate();
    }

    /**
     * Check if attribute is affecting or not
     *
     * @return bool
     */
    protected function isAffectingAttribute(): bool
    {
        foreach ($this->getProduct()->getVariantsAttributes() as $variantsAttribute) {
            if ($variantsAttribute->getId() === $this->getAttribute()->getId()) {
                return true;
            }
        }

        return false;
    }

    protected function getUnavailableTooltipTemplate(): string
    {
        return 'modules/XC/ProductVariants/product/attribute_value/select/block.unavailable.tooltip.twig';
    }

    protected function getSelectClasses(): string
    {
        return 'form-control';
    }
}
