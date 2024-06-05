<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Customer extends \CDev\FileAttachments\View\Product\Customer
{
    /**
     * Get attachments
     *
     * @return array
     */
    protected function getAttachments()
    {
        $list = parent::getAttachments();

        foreach ($list as $i => $attachment) {
            if ($attachment->getPrivate()) {
                unset($list[$i]);
            }
        }

        return array_values($list);
    }
}
