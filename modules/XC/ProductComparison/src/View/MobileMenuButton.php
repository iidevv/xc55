<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductComparison\View;

use XCart\Extender\Mapping\Extender;
use XC\ProductComparison\Core\Data;

/**
 * @Extender\Mixin
 */
class MobileMenuButton extends \XLite\View\MobileMenuButton
{
    /**
     * Get the list of mobile menu button CSS classes.
     */
    public function getClass(): string
    {
        $classes = array_filter(
            array_map(
                static fn(string $class): string => mb_strtolower($class),
                explode(" ", parent::getClass())
            )
        );
        if (!in_array('recently-updated', $classes, true)) {
            $data = Data::getInstance();
            if ($data->getProductsCount() > 0 && $data->isRecentlyUpdated()) {
                $classes[] = 'recently-updated';
            }
        }
        return implode(' ', $classes);
    }
}
