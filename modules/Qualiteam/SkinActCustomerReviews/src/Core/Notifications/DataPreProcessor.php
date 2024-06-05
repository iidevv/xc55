<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\Core\Notifications;


use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;
use XLite\Model\Repo\Product;

/**
 * DataPreProcessor
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XC\ThemeTweaker\Core\Notifications\DataPreProcessor
{
    public static function prepareDataForNotification($dir, array $data)
    {
        $data = parent::prepareDataForNotification($dir, $data);

        if ($dir === 'modules/Qualiteam/SkinActCustomerReviews/report_abuse') {
            $data = static::getDemoReportAbuse();
        }

        return $data;
    }

    protected static function getDemoReportAbuse()
    {
        $qb = Database::getRepo('\XC\Reviews\Model\Review')->createPureQueryBuilder();
        $review =  $qb->where('r.profile IS NOT NULL and r.product IS NOT NULL')->getSingleResult();

        return [$review];
    }

}