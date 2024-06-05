<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Library;

use CDev\GoogleAnalytics\Core\GA\ALibrary;
use CDev\GoogleAnalytics\Core\GA\JsList\Analytics as AnalyticsJsList;
use CDev\GoogleAnalytics\View\Header\Analytics as AnalyticsWidget;

class Analytics extends ALibrary
{
    protected static function tagWidgetClass(): string
    {
        return AnalyticsWidget::class;
    }

    protected static function jsListClass(): string
    {
        return AnalyticsJsList::class;
    }

    public function getScriptUrl(): string
    {
        return $this->isDebugMode()
            ? '//www.google-analytics.com/analytics_debug.js'
            : '//www.google-analytics.com/analytics.js';
    }

    protected function getTrackerConfig(): array
    {
        $config = [];

        switch ($this->getTrackingType()) {
            case 2:
                $config['cookieDomain'] = $this->getSubdomainCookieHost();
                break;
            case 3:
                $config['allowLinker'] = $this->getCrossDomainConfig();
                break;
        }

        return $config;
    }

    protected function getCrossDomainConfig(): bool
    {
        return true;
    }
}
