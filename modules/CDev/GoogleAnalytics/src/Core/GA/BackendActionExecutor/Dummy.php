<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\BackendActionExecutor;

use XLite\Base;
use CDev\GoogleAnalytics\Core\GA\Interfaces\IBackendActionExecutor;
use CDev\GoogleAnalytics\Logic\Action\Interfaces\IBackendAction;

class Dummy extends Base implements IBackendActionExecutor
{
    public function execute(IBackendAction $action): bool
    {
        return false;
    }
}
