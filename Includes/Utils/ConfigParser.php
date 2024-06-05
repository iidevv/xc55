<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

use XCart\Domain\StaticConfigDomain;

/**
 * ConfigParser
 *
 * @package    XLite
 */
class ConfigParser extends AUtils
{
    /**
     * Parse both config files
     *
     * @param array|string $names option names tree
     *
     * @return array|mixed
     */
    public static function getOptions($names = null)
    {
        /** @var StaticConfigDomain $configDomain */
        $configDomain = \XCart\Container::getContainer()->get(StaticConfigDomain::class);

        return $configDomain->getOption($names ?? []);
    }
}
