<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\Order\Details\Admin\Modifier;

/**
 * Sales tax modifier widget
 */
class Tax extends \XLite\View\Order\Details\Admin\Modifier
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/SalesTax/order/details/style.less';

        return $list;
    }
}
