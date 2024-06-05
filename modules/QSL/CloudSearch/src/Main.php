<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch;

use Exception;
use Includes\Utils\URLManager;
use XLite\Core\Config;
use QSL\CloudSearch\Core\ServiceApiClient;

/**
 * CloudSearch & CloudFilters module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check if CloudSearch is configured
     */
    public static function isConfigured(): bool
    {
        $apiClient = new ServiceApiClient();

        $apiKey    = $apiClient->getApiKey();
        $secretKey = $apiClient->getSecretKey();

        return !empty($apiKey) && !empty($secretKey);
    }

    /**
     * Check if CloudFilters is enabled
     */
    public static function isCloudFiltersEnabled(): bool
    {
        return in_array('cloudFilters', self::getPlanFeatures(), true)
            && Config::getInstance()->QSL->CloudSearch->isCloudFiltersEnabled;
    }

    /**
     * Check if realtime indexing is enabled
     */
    public static function isRealtimeIndexingEnabled(): bool
    {
        try {
            return in_array('realtimeIndexing', self::getPlanFeatures(), true);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if admin search is enabled
     */
    public static function isAdminSearchEnabled(): bool
    {
        return in_array('adminSearch', self::getPlanFeatures(), true)
            && Config::getInstance()->QSL->CloudSearch->isAdminSearchEnabled;
    }

    public static function getPlanFeatures(): array
    {
        $planFeatures = Config::getInstance()->QSL->CloudSearch->planFeatures;

        $planFeatures = !empty($planFeatures) ? json_decode($planFeatures, true) : [];

        return $planFeatures ?: [];
    }

    /**
     * Check if store is set up in multi-domain mode.
     * In multi-domain mode only the main domain will be registered in CS and all links will be indexed
     * as absolute URLs without host name so that every domain can use them properly.
     */
    public static function isMultiDomain(): bool
    {
        $domains = array_filter(URLManager::getShopDomains());

        return count($domains) > 1;
    }
}
