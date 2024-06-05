<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\Controller\Admin;

use Includes\Utils\Converter;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    public function handleRequest()
    {
        $module = $this->getModule();

        if ($module === 'Qualiteam-SkinActMagicImages') {
            $this->setReturnURL(
                Converter::buildURL(
                    'magic360_settings'
                )
            );
        }

        parent::handleRequest();
    }
}
