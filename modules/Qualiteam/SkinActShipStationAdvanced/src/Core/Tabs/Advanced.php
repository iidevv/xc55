<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Core\Tabs;

use Qualiteam\SkinActShipStationAdvanced\Traits\ShipstationAdvancedTrait;
use XLite\Core\Translation;

class Advanced extends ATabs
{
    use ShipstationAdvancedTrait;

    /**
     * @return string
     */
    public function getAllowedTarget(): string
    {
        return static::getMainConfigName();
    }

    /**
     * @return array
     */
    public function defineTabs(): array
    {
        return [
            static::getMainConfigName() => [
                'weight' => $this->getMainConfigWeight(),
                'title'  => Translation::lbl($this->getMainConfigTitleLabel()),
                'widget' => $this->getMainConfigViewWidgetClass(),
            ],
        ];
    }
}
