<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Core\Tabs;

use Qualiteam\SkinActShipStationAdvanced\Traits\ShipstationAdvancedTrait;
use XLite\Core\Translation;

class Statuses extends ATabs
{
    use ShipstationAdvancedTrait;

    /**
     * @return string
     */
    public function getAllowedTarget(): string
    {
        return static::getStatusesConfigName();
    }

    /**
     * @return array
     */
    public function defineTabs(): array
    {
        return [
            static::getStatusesConfigName() => [
                'weight' => $this->getStatusesWeight(),
                'title'  => Translation::lbl($this->getStatusesTitleLabel()),
                'widget' => $this->getStatusesViewWidgetClass(),
            ],
        ];
    }
}
