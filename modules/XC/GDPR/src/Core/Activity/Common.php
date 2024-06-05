<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core\Activity;

use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;
use XC\GDPR\Model\Repo\Activity as ActivityRepo;
use XC\GDPR\Model\Activity as ActivityModel;

class Common
{
    /**
     * @return ARepo|ActivityRepo
     */
    protected static function getRepo()
    {
        return Database::getRepo('\XC\GDPR\Model\Activity');
    }

    /**
     * @param $item
     * @param $type
     *
     * @return ActivityModel
     */
    protected static function createByItemAndType($item, $type)
    {
        $activity = new ActivityModel([
            'item'    => $item,
            'type'    => $type,
            'date'    => Converter::time(),
            'details' => [],
        ]);

        return $activity;
    }

    /**
     * @param $item
     * @param $type
     *
     * @return null|ActivityModel
     */
    protected static function findByItemAndType($item, $type)
    {
        return static::getRepo()->findOneBy([
            'item' => $item,
            'type' => $type,
        ]);
    }
}
