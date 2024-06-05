<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * APage
 * @Extender\Mixin
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Get tabs
     *
     * @return array
     */
    protected function getTabs()
    {
        $list = parent::getTabs();

        if ($this->getProduct()->isSnapshotProduct()) {
            $list = isset($list['Description'])
                ? [
                    'Description' => $list['Description'],
                ]
                : [];
        }

        return $list;
    }
}
