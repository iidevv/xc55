<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Tabs;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use Qualiteam\SkinActShipStationAdvanced\Core\Tabs\ATabs;
use XLite\Core\Translation;

class CodeMapping extends ATabs
{
    use AftershipTrait;

    public function getAllowedTarget(): string
    {
        return static::getCodeMappingConfigName();
    }

    /**
     * @return array
     */
    public function defineTabs(): array
    {
        return [
            static::getCodeMappingConfigName() => [
                'weight' => $this->getCodeMappingWeight(),
                'title'  => Translation::lbl($this->getCodeMappingTitleLabel()),
                'widget' => $this->getCodeMappingViewWidgetClass(),
            ],
        ];
    }
}