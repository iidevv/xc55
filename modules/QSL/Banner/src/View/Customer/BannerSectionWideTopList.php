<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Banner box widget
 *
 * @ListChild (list="layout.main", zone="customer", weight="395")
 */
class BannerSectionWideTopList extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/Banner/top_banners_list.twig';
    }
}
