<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product\AttributeValue\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Layout;

/**
 * Attribute value
 * @Extender\Mixin
 */
class Checkbox extends \XLite\View\Product\AttributeValue\Customer\Checkbox
{

    use  AttributeValueLinkedProductTrait;

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER
            && $this->hasLinkedProducts()
            && ($this->getTarget() != 'change_attribute_values' || !$this->hasAvailableLinkedProducts())
            ? 'modules/Qualiteam/SkinActLinkProductsToAttributes/product/attribute_value/checkbox/checkbox.twig'
            : parent::getTemplate();
    }

    protected function getSelectedAttributeValue()
    {
        $return = null;

        $selectedIds = $this->getSelectedIds();
        $id = $this->getAttribute()->getId();

        if (isset($selectedIds[$id])) {
            $return = Database::getRepo('XLite\Model\AttributeValue\AttributeValueCheckbox')->find($selectedIds[$id]);
        }

        return $return;
    }



}