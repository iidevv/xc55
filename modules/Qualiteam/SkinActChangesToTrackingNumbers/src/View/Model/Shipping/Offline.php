<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\View\Model\Shipping;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class Offline extends \XLite\View\Model\Shipping\Offline
{

    protected function getFieldsBySchema(array $schema)
    {
        $schemaTmp = [];
        foreach ($schema as $k => $v) {
            if ($k === 'deliveryTime') {
                $schemaTmp['instructions'] = [
                    self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Simple',
                    self::SCHEMA_LABEL => static::t('SkinActChangesToTrackingNumbers Instructions'),
                    self::SCHEMA_MODEL_ATTRIBUTES => [
                        \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 65532,
                    ],
                    self::SCHEMA_REQUIRED => false,
                ];
            }
            $schemaTmp[$k] = $v;
        }

        $schema = $schemaTmp;

        return parent::getFieldsBySchema($schema);
    }

}