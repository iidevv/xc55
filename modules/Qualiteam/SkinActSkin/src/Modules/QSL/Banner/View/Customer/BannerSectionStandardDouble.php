<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\Banner\View\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Banner box widget
 *
 * @Extender\Depend("QSL\Banner")
 *
 * @ListChild (list="center.bottom", zone="customer", weight="390")
 */
class BannerSectionStandardDouble extends \QSL\Banner\View\Customer\ABannerSection
{
    protected $location = 'StandardDouble';
}
