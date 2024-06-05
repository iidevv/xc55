<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Main menu
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SimpleCMS")
 * @Extender\After ("QSL\HorizontalCategoriesMenu")
 */
class Top extends \XLite\View\Menu\Customer\Top
{
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

        if ($item['depth'] === 0) {
            $classes[] = 'top-main-menu__item';
            $classes[] = 'top-main-menu__item--color--yellow';
        }

        if (count($this->getItems()) === ($index + 1)) {
            $classes[] = 'last';
        }

        if ($item['active']) {
            $classes[] = 'active';
        }

        if ($item['hasSubmenus']) {
            $classes[] = 'has-sub';
        }

        return $classes ? ' class="' . implode(' ', $classes) . '"' : '';
    }
}
