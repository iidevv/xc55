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
 * @ListChild (list="layout.top.wide", zone="customer", weight="10")
 */
class BannerSectionWideTop extends \QSL\Banner\View\Customer\ABannerSection
{
    protected $location = 'WideTop';
}
