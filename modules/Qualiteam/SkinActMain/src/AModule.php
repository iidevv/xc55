<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain;

use XCart\Container;

abstract class AModule extends \XLite\Module\AModule
{
    /**
     * @return string
     */
    public static function getModulePath(): string
    {
        return static::getCustomParameter(__FUNCTION__);
    }

    /**
     * @param string $param
     *
     * @return float|int|bool|array|string|null
     */
    protected static function getCustomParameter(string $param): float|int|bool|array|string|null
    {
        return Container::getContainer()->getParameter(
            static::prepareParameterName($param)
        );
    }

    /**
     * @param string $parameterName
     *
     * @return string
     */
    protected static function prepareParameterName(string $parameterName): string
    {
        [$author, $name] = explode('-', static::getId());

        return sprintf('%s.%s.%s',
            $author,
            $name,
            $parameterName
        );
    }
}