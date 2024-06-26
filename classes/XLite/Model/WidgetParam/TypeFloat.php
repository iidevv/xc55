<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * ____description____
 */
class TypeFloat extends \XLite\Model\WidgetParam\AWidgetParam
{
    /**
     * Param type
     *
     * @var string
     */
    protected $type = 'float';

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return array
     */
    protected function getValidationSchema($value)
    {
        return [
            [
                static::ATTR_CONDITION => !preg_match('/^\s*[-+]?[0-9]*\.?[0-9]+$/Ss', $value),
                static::ATTR_MESSAGE   => ' is not float',
            ],
        ];
    }
}
