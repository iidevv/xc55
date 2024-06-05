<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    protected function processAddItemSuccess($item)
    {
        $isReorder = \XLite\Core\Request::getInstance()->is_reorder;
        if ($isReorder) {
            $attributes = [];

            foreach ($item->getAttributeValues() as $attributeValue) {
                $attributes[] = sprintf('%s: %s', $attributeValue->getActualName(), $attributeValue->getActualValue());
            }

            \XLite\Core\TopMessage::addInfo('SkinActProductReOrdering re-order product has been added to cart', [
                'attributes' => implode(', <br />', $attributes),
            ]);

            \XLite\Core\Event::productAddedToCart($this->assembleProductAddedToCartEvent($item));
        } else {
            parent::processAddItemSuccess($item);
        }
    }
}