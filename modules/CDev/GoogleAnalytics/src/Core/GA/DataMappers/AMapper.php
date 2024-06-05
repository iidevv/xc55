<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA\DataMappers;

use CDev\GoogleAnalytics\Core\GA\Interfaces\DataMappers\ICommon;

abstract class AMapper
{
    /**
     * @var ICommon
     */
    protected $instance;

    public function __construct(ICommon $instance = null)
    {
        $this->instance = $instance;
    }

    protected static function map(array $data, array $keys = []): array
    {
        foreach ($keys ?: static::keys() as $from => $to) {
            if (!isset($data[$from])) {
                continue;
            }
            $data[$to] = $data[$from];
            unset($data[$from]);
        }

        return $data;
    }

    protected static function keys(): array
    {
        return [];
    }

    /**
     * @return mixed
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function __call($method, array $args = [])
    {
        return [];
    }
}
