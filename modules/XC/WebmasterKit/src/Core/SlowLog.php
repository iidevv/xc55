<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\Core;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\InjectLoggerTrait;

class SlowLog extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;
    use InjectLoggerTrait;

    protected function getDurationForLong()
    {
        return $this->executeCachedRuntime(static function () {
            $duration = (int) \XLite\Core\Config::getInstance()->XC->WebmasterKit->slowLogQueryDuration ?: 1000;
            return $duration / 1000;
        });
    }

    public function logQuery($sql, $duration, array $backtrace)
    {
        if ($duration > $this->getDurationForLong()) {
            $this->getLogger('slow_log')->debug($sql, [
                'Duration' => round($duration, 4) . 'sec.',
                'trace' => true
            ]);
        }
    }
}
