<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Menu\Customer;

use Symfony\Component\Filesystem\Filesystem;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\Menu\Customer\ACustomer
{
    /**
     * Prepare items
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function prepareItems($items)
    {
        $items = parent::prepareItems($items);

        if (static::isAMP()) {
            $fs = new Filesystem();

            foreach ($items as &$item) {
                if (isset($item['url']) && !$fs->isAbsolutePath($item['url'])) {
                    $item['url'] = $this->getAbsoluteURL($item['url']);
                }
            }
            unset($item);
        }

        return $items;
    }
}
