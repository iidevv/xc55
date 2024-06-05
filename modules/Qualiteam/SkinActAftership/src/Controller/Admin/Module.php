<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Controller\Admin;

use Includes\Utils\Converter;
use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Module extends \XLite\Controller\Admin\Module
{
    use AftershipTrait;

    public function handleRequest()
    {
        $module = $this->getModule();

        if ($module === 'Qualiteam-SkinActAftership') {
            $this->setReturnURL(
                Converter::buildURL(
                    self::getMainConfigName()
                )
            );
        }

        parent::handleRequest();
    }
}
