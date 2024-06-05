<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\FormModel;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class FormGenerator extends \XLite\View\FormModel\FormGenerator
{
    /**
     * @return array
     */
    protected function getTypeExtensions()
    {
        $list = parent::getTypeExtensions();
        $list[] = 'QSL\ProductStickers\View\FormModel\Type\Base\ChoiceTypeExtension';

        return $list;
    }
}
