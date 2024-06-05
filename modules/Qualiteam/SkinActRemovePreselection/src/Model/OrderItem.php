<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActRemovePreselection\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{

    public function isValid()
    {
        $isValid = parent::isValid();

        if (!$isValid && !\XLite\Model\Cart::getInstance()->hasInvalidAttributes) {

            $result = $this->getProduct()->getEnabled() && 0 < $this->getAmount();

            if ($result && $this->getProduct()->isUpcomingProduct()) {
                $result = $this->getProduct()->isAllowedUpcomingProduct();
            }

            if (
                $result
                && (
                    $this->hasAttributeValues()
                    || $this->getProduct()->hasEditableAttributes()
                )
            ) {
                $result = array_keys($this->getAttributeValuesIds()) == $this->getProduct()->getEditableAttributesIds();

                if ($result === false) {

                    \XLite\Model\Cart::getInstance()->hasInvalidAttributes = true;

                }

            }

        }

        return $isValid;
    }
}