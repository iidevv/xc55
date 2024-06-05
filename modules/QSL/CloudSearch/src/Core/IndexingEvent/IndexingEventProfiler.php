<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core\IndexingEvent;

use XLite\InjectLoggerTrait;

class IndexingEventProfiler extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    protected $sendTime = 0;

    protected $totalTime = 0;

    public function addToTotalTime($t)
    {
        $this->totalTime += $t;
    }

    public function addToSendTime($t)
    {
        $this->sendTime += $t;
    }

    public function log()
    {
        $totalTime = round($this->totalTime * 1000);
        $sendTime  = round($this->sendTime * 1000);

        if ($totalTime > 10) {
            $this->getLogger('CloudSearchEvents')->info("$totalTime $sendTime");
        }
    }
}
