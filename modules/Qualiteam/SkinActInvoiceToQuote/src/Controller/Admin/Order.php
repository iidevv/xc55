<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActInvoiceToQuote\Controller\Admin;

use Qualiteam\SkinActInvoiceToQuote\Main;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class order
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $pages = parent::getPages();

        if (Main::isShowCustomLabel($this->getOrder())
            && isset($pages['invoice'])
        ) {
            $pages['invoice'] = static::t('SkinActInvoiceToQuote quote');
        }

        return $pages;
    }
}