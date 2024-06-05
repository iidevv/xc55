<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Tabs;

use XCart\Container;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class TabsFactory extends \Qualiteam\SkinActShipStationAdvanced\Core\Tabs\TabsFactory
{
    public function __construct()
    {
        parent::__construct();

        $this->tabs += [
            'codeMapping' => Container::getContainer()->get('aftership.tabs.codeMapping'),
        ];
    }
}