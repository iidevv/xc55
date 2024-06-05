<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\Library;

use CDev\GoogleAnalytics\Core\GA\ALibrary;
use CDev\GoogleAnalytics\Core\GA\JsList\GTag as GTagJsList;
use CDev\GoogleAnalytics\View\Header\GTag as GTagWidget;

class GTag extends ALibrary
{
    protected static function tagWidgetClass(): string
    {
        return GTagWidget::class;
    }

    protected static function jsListClass(): string
    {
        return GTagJsList::class;
    }

    public function getScriptUrl(): string
    {
        return 'https://www.googletagmanager.com/gtag/js?id=' . $this->getMeasurementId();
    }

    protected function getTrackerConfig(): array
    {
        $config = [];

        if (!$this->isSendPageviewActive()) {
            $config['send_page_view'] = false;
        }

        if ($this->isDebugMode()) {
            $config['debug_mode'] = true;
        }

        switch ($this->getTrackingType()) {
            case 2:
                $config['cookie_domain'] = $this->getSubdomainCookieHost();
                break;
            case 3:
                $config['linker'] = $this->getCrossDomainConfig();
                break;
        }

        return $config;
    }

    protected function getCrossDomainConfig(): array
    {
        return ['accept_incoming' => true];
    }

    protected function getTagWidgetParams(): array
    {
        return array_merge_recursive(parent::getTagWidgetParams(), [
            GTagWidget::PARAM_GTAG_OPTIONS => $this->getGTagOptions(),
        ]);
    }

    protected function getGTagOptions(): array
    {
        return [];
    }
}
