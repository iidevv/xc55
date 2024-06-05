<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\Model\Shipping;

use Qualiteam\SkinActAftership\View\FormField\Select\Select2\Couriers;
use XCart\Extender\Mapping\Extender;

/**
 * Class offline
 *
 * @Extender\Mixin
 * @Extender\Before("Qualiteam\SkinActChangesToTrackingNumbers")
 */
class Offline extends \XLite\View\Model\Shipping\Offline
{
    protected function getFieldsBySchema(array $schema)
    {
        $arr = [];

        foreach ($schema as $key => $item) {
            if ($key === 'deliveryTime') {
                $arr['aftership_couriers'] = [
                    self::SCHEMA_CLASS => Couriers::class,
                    self::SCHEMA_LABEL => static::t('SkinActAftership couriers'),
                    self::SCHEMA_HELP  => static::t('SkinActAftership couriers help'),
                ];
            }

            $arr[$key] = $item;
        }

        $schema = $arr;

        return parent::getFieldsBySchema($schema);
    }
}
