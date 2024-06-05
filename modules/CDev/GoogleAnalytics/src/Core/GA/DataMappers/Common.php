<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\DataMappers;

use XLite\Core\Cache\ExecuteCachedTrait;
use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\ICommon;

class Common implements ICommon
{
    use ExecuteCachedTrait;
}
