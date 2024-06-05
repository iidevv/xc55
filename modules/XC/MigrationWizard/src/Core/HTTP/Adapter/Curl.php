<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Core\HTTP\Adapter;

use XCart\Extender\Mapping\Extender;

/**
 * Curl
 * @Extender\Mixin
 */
class Curl extends \XLite\Core\HTTP\Adapter\Curl
{
    /**
     * The number of seconds to wait while trying to connect
     *
     * @var integer
     */
    protected $connectTimeout = 7; // reduce default timeout for better experience
}
