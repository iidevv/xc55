<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory;

use Psr\Log\LoggerInterface;
use XLite\Logger;

class LoggerFactory
{
    /**
     * @return LoggerInterface
     */
    public static function logger(): LoggerInterface
    {
        return Logger::getLogger('yotpo.reviews.logger');
    }
}