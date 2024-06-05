<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Customer;

/**
 * Abstract customer menu
 */
abstract class ACustomer extends \XLite\View\Menu\AMenu
{
    /**
     * Mark selected
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function markSelected($items)
    {
        foreach ($items as $k => $v) {
            $items[$k]['active'] = $this->isActiveItem($v);
        }

        return $items;
    }

    /**
     * Prepare items
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function prepareItems($items)
    {
        return $items;
    }

    /**
     * Check - specified item is active or not
     *
     * @param array $item Menu item
     *
     * @return boolean
     */
    protected function isActiveItem(array $item)
    {
        return false;
    }

    /**
     * Display item class as tag attribute
     *
     * @param integer $index Item index
     * @param mixed   $item  Item element
     *
     * @return string
     */
    protected function displayItemClass($index, $item)
    {
        $classes = ['leaf'];
        $items = $this->getItems();

        if ($index === array_key_first($items)) {
            $classes[] = 'first';
        }

        if ($index === array_key_last($items)) {
            $classes[] = 'last';
        }

        if ($item['active']) {
            $classes[] = 'active';
        }

        return $classes ? ' class="' . implode(' ', $classes) . '"' : '';
    }
}
