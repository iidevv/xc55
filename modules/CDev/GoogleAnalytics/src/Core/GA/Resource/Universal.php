<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Resource;

use CDev\GoogleAnalytics\Core\GA\AResource;

class Universal extends AResource
{
    public function getMeasurementId(): string
    {
        return $this->config->ga_account;
    }

    public function getBackendExecutorClass(): string
    {
        return \CDev\GoogleAnalytics\Core\GA\BackendActionExecutor\Universal::class;
    }
}
