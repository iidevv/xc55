<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Traits;

use Qualiteam\SkinActShipStationAdvanced\View\Admin\ShipstationAdvanced as ConfigView;
use Qualiteam\SkinActShipStationAdvanced\View\Admin\ShipstationStatuses as StatusesView;

trait ShipstationAdvancedTrait
{
    protected static function getMainConfigName(): string
    {
        return 'shipstation_advanced';
    }

    protected static function getStatusesConfigName(): string
    {
        return 'shipstation_statuses';
    }

    protected function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActShipStationAdvanced';
    }

    protected function getStatusesTitleLabel(): string
    {
        return 'SkinActShipStationAdvanced statuses';
    }

    protected function getMainConfigTitleLabel(): string
    {
        return 'SkinActShipStationAdvanced general';
    }

    protected function getStatusesViewWidgetClass(): string
    {
        return StatusesView::class;
    }

    protected function getMainConfigViewWidgetClass(): string
    {
        return ConfigView::class;
    }

    protected function getMainConfigWeight(): int
    {
        return 100;
    }

    protected function getStatusesWeight(): int
    {
        return 200;
    }
}