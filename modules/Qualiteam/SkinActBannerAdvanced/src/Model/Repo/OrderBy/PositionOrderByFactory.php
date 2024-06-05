<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy;

class PositionOrderByFactory extends OrderByFactory
{
    protected function getOrderByContracts()
    {
        return [
            Position::class,
            MobilePosition::class
        ];
    }
}