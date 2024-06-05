<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View;

use XCart\Extender\Mapping\Extender;

/**
 * Cart widget
 * @Extender\Mixin
 */
class Cart extends \XLite\View\Cart
{
    /**
     * Get groups of cart items
     *
     * @return array
     */
    protected function getCartItemsGroups()
    {

        $groups = parent::getCartItemsGroups();

        $newGroups = [];

        if ($groups) {
            foreach ($groups as $group) {
                $newItems = [];
                foreach ($group['items'] as $item) {

                    if(!$item->getParentItem()) {
                        $newItems[] = $item;
                    }

                    if ($item->hasLinkedOrderItems()) {
                        $newItems = array_merge($newItems, $item->getLinkedItems()->toArray());
                    }

                }
                $newGroup['items'] = $newItems;

                $newGroups[] = $newGroup;
            }
        }

        return $newGroups;
    }

}