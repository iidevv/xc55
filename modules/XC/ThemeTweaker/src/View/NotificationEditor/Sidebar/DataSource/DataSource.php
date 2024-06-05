<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;

use XC\ThemeTweaker\Core\Notifications\Data;

interface DataSource
{
    /**
     * @param Data $data
     *
     * @return boolean
     */
    public static function isApplicable(Data $data);

    /**
     * @param Data $data
     *
     * @return static
     */
    public static function buildNew(Data $data);
}
