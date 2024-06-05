<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Controller\Admin;

use Includes\Utils\Converter;
use Qualiteam\SkinActShipStationAdvanced\Traits\ShipstationAdvancedTrait;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    use ShipstationAdvancedTrait;

    public function handleRequest()
    {
        $module = $this->getModule();

        if ($module === 'ShipStation-Api') {
            $this->setReturnURL(
                Converter::buildURL(
                    self::getMainConfigName()
                )
            );
        }

        parent::handleRequest();
    }
}
