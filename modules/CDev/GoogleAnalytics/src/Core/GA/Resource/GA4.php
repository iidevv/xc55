<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Resource;

use CDev\GoogleAnalytics\Core\GA\AResource;

class GA4 extends AResource
{
    public function isConfigured(): bool
    {
        return parent::isConfigured() && ($this->isPurchaseImmediatelyOnSuccess() || $this->getApiSecret());
    }

    public function getApiSecret(): string
    {
        return $this->config->ga_api_secret;
    }

    public function getMeasurementId(): string
    {
        return $this->config->ga_measurement_id;
    }

    public function getBackendExecutorClass(): string
    {
        return \CDev\GoogleAnalytics\Core\GA\BackendActionExecutor\GA4::class;
    }
}
