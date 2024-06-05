<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail;

interface IMail
{
    /**
     * @return string
     */
    public static function getZone();

    /**
     * @return string
     */
    public static function getDir();
}
