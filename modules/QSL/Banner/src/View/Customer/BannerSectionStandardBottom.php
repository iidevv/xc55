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
 * @ListChild (list="center.bottom", zone="customer", weight="2000")
 */
class BannerSectionStandardBottom extends \QSL\Banner\View\Customer\ABannerSection
{
    protected $location = 'StandardBottom';
}
