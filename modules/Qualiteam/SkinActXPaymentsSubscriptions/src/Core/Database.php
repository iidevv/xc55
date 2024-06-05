<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Database
 *
 * @Extender\Mixin
 */
abstract class Database extends \XLite\Core\Database
{
    /**
     * Post process schemas
     *
     * @param array  $schemas Schemas
     * @param string $mode    Schema generation mode OPTIONAL
     *
     * @return array
     */
    protected function postprocessSchema($schemas, $mode = self::SCHEMA_CREATE)
    {
        $result = parent::postprocessSchema($schemas, $mode);

        $tmp1 = [];
        $tmp2 = [];

        foreach ($result as $query) {
            if (false === strpos($query, 'ADD CONSTRAINT')) {
                $tmp1[] = $query;
            } else {
                $tmp2[] = $query;
            }
        }

        return array_merge($tmp1, $tmp2);
    }
}
