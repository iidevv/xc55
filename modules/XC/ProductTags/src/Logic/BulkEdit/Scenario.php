<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Logic\BulkEdit;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\BulkEditing")
 */
class Scenario extends \XC\BulkEditing\Logic\BulkEdit\Scenario
{
    /**
     * @return array
     */
    protected static function defineScenario()
    {
        $result = parent::defineScenario();
        $result['product_categories']['title'] = \XLite\Core\Translation::getInstance()->translate('Categories and tags');

        $result['product_categories']['fields']['default']['tags'] = [
            'class'   => 'XC\ProductTags\Logic\BulkEdit\Field\Product\Tag',
            'options' => [
                'position' => 200,
            ],
        ];

        return $result;
    }
}
