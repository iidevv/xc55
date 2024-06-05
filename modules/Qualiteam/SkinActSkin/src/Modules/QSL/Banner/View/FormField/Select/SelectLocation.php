<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\Banner\View\FormField\Select;

use XCart\Extender\Mapping\Extender;

/**
 * Banner System selector
 *
 * @Extender\Mixin
 * @Extender\Depend("QSL\Banner")
 */
class SelectLocation extends \QSL\Banner\View\FormField\Select\SelectLocation
{

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();

        $options['StandardMiddle'] = static::t('[SkinActSkin] Middle banner');
        $options['StandardDouble'] = static::t('[SkinActSkin] Double banner');

        return $options;
    }

}
