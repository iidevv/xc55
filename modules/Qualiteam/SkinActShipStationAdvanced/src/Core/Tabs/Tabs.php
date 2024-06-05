<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Core\Tabs;

class Tabs
{
    protected array $tabs;
    protected array $targets;

    /**
     * @param array $tabs
     * @param array $targets
     */
    public function __construct(array $tabs, array $targets)
    {
        $this->tabs    = $tabs;
        $this->targets = $targets;
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }
}