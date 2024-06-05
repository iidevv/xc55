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
class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{

    use AttributeValueLinkedProductTrait;

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER && $this->hasAvailableLinkedProducts() && $this->getTarget() != 'change_attribute_values'
            ? 'modules/Qualiteam/SkinActLinkProductsToAttributes/product/attribute_value/select/selectbox.twig'
            : parent::getTemplate();
    }

    protected function getSelectedAttributeValue()
    {
        $return = null;

        $selectedIds = $this->getSelectedIds();
        $id = $this->getAttribute()->getId();

        if (isset($selectedIds[$id])) {
            $return = Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect')->find($selectedIds[$id]);
        }

        return $return;
    }

    /**
     * Return field value
     *
     * @return mixed|null
     */
    protected function getAttributeValue()
    {
        $attributeValues = parent::getAttributeValue();

        foreach ($attributeValues as $key => $value) {
            if ($value->getLinkedProduct() && !$value->getLinkedProduct()->isPublicAvailable()) {
                unset($attributeValues[$key]);
            }
        }

        return $attributeValues;
    }
}