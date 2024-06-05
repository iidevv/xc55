<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed;

use Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Generator;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\URLManager;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return Converter::buildURL('google_product_rating_feed');
    }

    /**
     * Returns public available google feed url
     */
    public static function getGoogleProductRatingFeedUrl()
    {
        if (!Generator::getInstance() || !Generator::getInstance()->isGenerated()) {
            return null;
        }

        $params = [
            'key' => !empty(static::getFeedKey()) ? static::getFeedKey() : '',
        ];

        return Converter::buildFullURL(
            'google_product_rating_feed',
            '',
            $params,
            \XLite::CART_SELF
        );
    }

    /**
     * Returns HTTPS-ready absolute url without xid parameter
     *
     * @param string $url    Inner URL part
     * @param array  $params Query params
     *
     * @return string
     */
    public static function getShopURL($url, $params = [])
    {
        return URLManager::getShopURL(
            $url,
            true,
            $params,
            null,
            false
        );
    }

    /**
     * Return google feed key
     *
     * @return string|null
     */
    protected static function getFeedKey(): ?string
    {
        return Config::getInstance()->Qualiteam->SkinActGoogleProductRatingFeed->google_rating_feed_key;
    }

    /**
     * Slice array and put new array part
     *
     * @param array  $array
     * @param string $keyName
     * @param array  $newPart
     * @param bool   $after
     *
     * @return array
     */
    public static function sliceArray(array $array, string $keyName, array $newPart, bool $after = false): array
    {
        $keyId = array_search($keyName, array_keys($array));

        $keyId = $after ? ++$keyId : $keyId;

        $partOne   = array_slice($array, 0, $keyId, true);
        $partTwo   = $newPart;
        $partThree = array_slice($array, $keyId, count($array) - $keyId, true);

        return array_merge($partOne, $partTwo, $partThree);
    }
}
