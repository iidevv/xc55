<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\Core\Marketplace\Constant;
use XLite\Core\Cache\ExecuteCached;

/**
 * Marketplace
 */
class Marketplace extends \XLite\Base\Singleton
{
    /**
     * Dedicated return code for the "performActionWithTTL" method
     */
    public const TTL_NOT_EXPIRED = '____TTL_NOT_EXPIRED____';

    /**
     * Some predefined TTLs
     */
    public const TTL_LONG  = 86400;
    public const TTL_SHORT = 3600;

    /**
     * HTTP request TTL for 'test_marketplace' action
     */
    public const TTL_TEST_MP = 300; // 5 minutes

    /**
     * PurchaseURL host
     */
    public const PURCHASE_URL_HOST = 'market.x-cart.com';

    protected $systemData;

    protected $segmentData;

    protected $serviceStorage = [];

    /**
     * @return string
     */
    public static function getBusinessPurchaseURL()
    {
        return static::getPurchaseURL();
    }

    /**
     * @param int   $id
     * @param array $params
     * @param bool  $ignoreId
     *
     * @return string
     */
    public static function getPurchaseURL($id = 0, array $params = [], $ignoreId = false)
    {
        $adminEmail = \XLite\Core\Auth::getInstance()->isAdmin()
            ? \XLite\Core\Auth::getInstance()->getProfile()->getLogin()
            : null;

        $controllerTarget = \XLite::isAdminZone() && \XLite::getController()
            ? \XLite::getController()->getTarget()
            : '';

        $shopUrl = \XLite\Core\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL()
        );

        return \Includes\Utils\URLManager::getPurchaseURL(
            $shopUrl,
            $controllerTarget,
            \XLite::getAffiliateId(),
            \XLite::getInstallationLng(),
            $adminEmail,
            $id,
            $params,
            $ignoreId
        );
    }

    /**
     * This function defines original link to X-Cart.com site's Contact Us page
     *
     * @return string
     */
    public static function getContactUsURL()
    {
        return \XLite::getXCartURL('https://www.x-cart.com/contact-us.html');
    }

    /**
     * This function defines original link to X-Cart.com site's License Agreement page
     *
     * @return string
     */
    public static function getLicenseAgreementURL()
    {
        return \XLite::getXCartURL('https://www.x-cart.com/license-agreement.html');
    }

    /**
     * @return array
     */
    public function getSystemData()
    {
        return [];
    }

    /**
     * @return \Exception|null
     */
    public function getLastError()
    {
        return Marketplace\Retriever::getInstance()->getLastError();
    }

    /**
     * @return array
     */
    public function getPaymentMethods(string $countryCode = '')
    {
        $params = $countryCode
            ? ['shopCountryCode' => $countryCode]
            : [];

        return MarketplaceClient::getInstance()->retrieve(
            'get_payment_methods',
            $params
        );
    }

    /**
     * Update payment methods
     *
     * @param integer|null $ttl TTL
     */
    public function updatePaymentMethods($countryCode, $ttl = null)
    {
        $countryCode = $countryCode ?: \XLite\Core\Config::getInstance()->Company->location_country;
        [$cellTTL,] = $this->getActionCacheVars(Constant::REQUEST_PAYMENT_METHODS . '-' . $countryCode . '-');

        $ttl = $ttl ?? static::TTL_LONG;

        // Check if expired
        if (!$this->checkTTL($cellTTL, $ttl) || $this->isServiceCacheReset($cellTTL, 'paymentMethodsCacheDate')) {
            if ($data = $this->getPaymentMethods($countryCode)) {
                \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->updatePaymentMethods($data, $countryCode);
                $this->setTTLStart($cellTTL);
            }
        }
    }

    /**
     * @return array
     */
    public function getShippingMethods()
    {
        return MarketplaceClient::getInstance()->retrieve('get_shipping_methods');
    }

    /**
     * Update shipping methods
     *
     * @param integer|null $ttl TTL
     */
    public function updateShippingMethods($ttl = null)
    {
        [$cellTTL,] = $this->getActionCacheVars(Constant::REQUEST_SHIPPING_METHODS);

        $ttl = $ttl ?? static::TTL_LONG;

        // Check if expired
        if (!$this->checkTTL($cellTTL, $ttl) || $this->isServiceCacheReset($cellTTL, 'shippingMethodsCacheDate')) {
            if ($data = $this->getShippingMethods()) {
                \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->updateShippingMethods($data);
                $this->setTTLStart($cellTTL);
            }
        }
    }

    /**
     * Get actions list for 'get_dataset' request
     *
     * @return array
     */
    public function getActionsForGetDataset()
    {
        $actions = array_fill_keys($this->getExpiredActions(), []);

        $scheduled = $this->getScheduledActions();

        if ($scheduled) {
            $actions = array_merge($actions, $scheduled);
        }

        return $actions;
    }

    // {{{ "Get dataset" request

    /**
     * Return true if action is active (non-empty and not expired)
     *
     * @param string $action Action type
     *
     * @return boolean
     */
    public function isActionActive($action)
    {
        [$cellTTL,] = $this->getActionCacheVars($action);

        return !$this->checkTTL($cellTTL, $this->getActionTTL($action));
    }

    // }}}

    /**
     * @return array
     */
    public function getAccountingModules()
    {
        // todo: implement it on frontend
        return [];
    }

    /**
     * @return bool
     */
    public function hasAvailableNotInstalledMarketingModules()
    {
        $criteria = [
            'installed'      => false,
            'canInstall'     => true,
            'system'         => false,
            'isSalesChannel' => true,
        ];

        $systemData = $this->getSystemData();
        $dataDate   = $systemData['dataDate'] ?? 0;

        $cacheKeyData = $criteria + [
                'dataDate' => $dataDate,
            ];

        return $this->performRequestWithCache($cacheKeyData, static function () use ($criteria) {
            $modules = \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
                \XLite\Core\Marketplace\QueryRegistry::getQuery('marketplace_modules', $criteria),
                new \XLite\Core\Marketplace\Normalizer\MarketplaceModules()
            );

            return !empty($modules);
        });
    }

    /**
     * @param bool $withCore
     *
     * @return array
     * @todo: cache
     */
    public function getHashMap($withCore = true)
    {
        $entries = $this->getUpgradeTypesEntries();

        $listToCheckInOrder = [
            'build',
            'minor',
            'major',
            'core', // Means 1 number changes, e.g. 5.x.x.x to 6.x.x.x
        ];

        $result = array_merge(
            [
                'total'      => 0,
                'core-types' => [],
            ],
            array_fill_keys($listToCheckInOrder, 0)
        );

        foreach ($listToCheckInOrder as $type) {
            $entriesByType = $entries[$type] ?? [];
            if (
                (isset($entriesByType['CDev-Core']) && $entriesByType['CDev-Core']['type'] === $type)
                || (isset($entries['self']['XC-Service']) && $entries['self']['XC-Service']['type'] === $type)
            ) {
                $result['core-types'][] = $type;
            }

            $entriesOfType = array_filter(
                $entriesByType,
                static function ($entry) use ($withCore, $type) {
                    return $entry['type'] === $type && ($withCore || $entry['id'] !== 'CDev-Core');
                }
            );

            $previousTypeIndex = (int) $type - 1;
            $previousTypeCount = $result[$previousTypeIndex] ?? 0;
            $countOnlyThisType = count($entriesOfType) - $previousTypeCount;
            $result[$type]     = $countOnlyThisType;
            $result['total']   += $countOnlyThisType;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getUpgradeTypesEntries()
    {
        $systemData = $this->getSystemData();
        $dataDate   = $systemData['dataDate'] ?? 0;

        return $this->performRequestWithCache(
            ['getUpgradeTypesEntries', $dataDate],
            static function () {
                $data = \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
                    \XLite\Core\Marketplace\QueryRegistry::getQuery('upgrade_entries'),
                    new \XLite\Core\Marketplace\Normalizer\Raw()
                );

                if (!$data) {
                    return [];
                }

                foreach ($data as $key => $datum) {
                    $datum      = $datum ?: [];
                    $keys       = array_map(static function ($type) {
                        return $type['id'];
                    }, $datum);
                    $data[$key] = array_combine($keys, $datum);
                }

                return $data;
            }
        );
    }

    /**
     * @return boolean
     */
    public function isFraud()
    {
        $systemData = $this->getSystemData();

        return $systemData[Constant::FIELD_IS_CONFIRMED] ?? false;
    }

    /**
     * @return array
     */
    public function dropRebuild()
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getMutation('dropRebuild'),
            new \XLite\Core\Marketplace\Normalizer\Simple('dropRebuild')
        ) ?: [];
    }

    /**
     * @return array
     */
    public function clearCache()
    {
        $driver = \XLite\Core\Cache::getInstance()->getDriver();

        $driver->delete(ExecuteCached::getCacheKey([Constant::REQUEST_CORE_LICENSE, 0]));
        $driver->delete(ExecuteCached::getCacheKey([Constant::REQUEST_OUTDATED_MODULE, 0]));
        $driver->delete(ExecuteCached::getCacheKey([Constant::REQUEST_INSTALLATION_DATA, 0]));
        $driver->delete(ExecuteCached::getCacheKey(['getUpgradeTypesEntries', 0]));

        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getMutation('clearCache'),
            new \XLite\Core\Marketplace\Normalizer\Simple('clearCache')
        ) ?: [];
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function createScenario($type = 'common')
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getMutation('createScenario', [
                'type' => $type,
            ]),
            new \XLite\Core\Marketplace\Normalizer\Simple('createScenario')
        ) ?: [];
    }

    /**
     * @param string $scenarioId
     * @param array  $states
     *
     * @return array
     */
    public function changeModulesState($scenarioId, $states)
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getRaw('changeModulesState', null, [
                'scenarioId' => $scenarioId,
                'states'     => $states,
            ]),
            new \XLite\Core\Marketplace\Normalizer\Simple('changeModulesState')
        ) ?: [];
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getRebuildState($id)
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getQuery('rebuildState', [
                'id' => $id,
            ]),
            new \XLite\Core\Marketplace\Normalizer\Simple('rebuildState')
        ) ?: [];
    }

    /**
     * @param string $id
     * @param array  $options OPTIONAL
     *
     * @return array
     */
    public function startRebuild($id, $options = [])
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getMutation(
                'startRebuild',
                array_merge($options, ['id' => $id])
            ),
            new \XLite\Core\Marketplace\Normalizer\Simple('startRebuild')
        ) ?: [];
    }

    /**
     * @param string $id
     * @param string $action
     *
     * @return array
     */
    public function executeRebuild($id, $action = 'execute')
    {
        return \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getMutation('executeRebuild', [
                'id'     => $id,
                'action' => $action,
            ]),
            new \XLite\Core\Marketplace\Normalizer\Simple('executeRebuild')
        ) ?: [];
    }

    /**
     * Check if cache was reset by service.php?/clear-cache
     *
     * @param $cell
     * @param $serviceVar
     *
     * @return bool
     */
    protected function isServiceCacheReset($cell, $serviceVar)
    {
        $start      = \XLite\Core\TmpVars::getInstance()->$cell;
        $systemData = $this->getSystemData();

        return isset($start)
            && isset($systemData[$serviceVar])
            && $start < $systemData[$serviceVar];
    }

    // {{{ "Get xc5 notifications" request

    /**
     * Get list of expired actions
     *
     * @return array
     */
    protected function getExpiredActions()
    {
        return array_filter(array_keys($this->getMarketplaceActions()), [$this, 'isActionActive']);
    }

    /**
     * Get list of actions which cannot be issued in the 'get_dataset' request
     *
     * @return array
     */
    protected function getDatasetExcludedActions()
    {
        return [
            Constant::REQUEST_ADDON_HASH,
            Constant::REQUEST_ADDON_INFO,
            Constant::REQUEST_ADDON_PACK,
            Constant::REQUEST_CORE_HASH,
            Constant::REQUEST_CORE_PACK,
            Constant::REQUEST_OUTDATED_MODULE,
            Constant::REQUEST_PAYMENT_METHODS,
            Constant::REQUEST_RESEND_KEY,
            Constant::REQUEST_SET,
            Constant::REQUEST_SET_KEY_WAVE,
            Constant::REQUEST_SHIPPING_METHODS,
            Constant::REQUEST_TEST,
        ];
    }

    // }}}

    // {{{ Cache-related routines

    /**
     * Process result of 'get_dataset' request
     *
     * @param array $responseData Result data of 'get_dataset' request
     *
     * @return array
     */
    protected function processGetDatasetResult($responseData)
    {
        if (is_array($responseData)) {
            foreach ($responseData as $action => $data) {
                // $result is NULL when nothing is received from the marketplace
                if (is_array($data)) {
                    $saveInTmpVars = true;

                    $this->saveResultInCache($action, $data, $saveInTmpVars);
                }
            }
        }

        return $responseData;
    }

    protected function performRequestWithCache($cacheParams, callable $callback, $ttl = 86400)
    {
        return ExecuteCached::executeCached(
            $callback,
            $cacheParams,
            $ttl
        );
    }

    protected function performActionRequestWithCache($requestName, callable $callback)
    {
        \XLite\Core\Lock\MarketplaceLocker::getInstance()->waitForUnlocked($requestName);
        \XLite\Core\Lock\MarketplaceLocker::getInstance()->lock($requestName);

        if (!$this->isActionActive($requestName)) {
            [, $dataCell] = $this->getActionCacheVars($requestName);
            $result = \XLite\Core\TmpVars::getInstance()->$dataCell;

            $this->scheduleAction($requestName, []);
        } else {
            $result = $callback();
            $this->saveResultInCache($requestName, $result, true);
        }

        \XLite\Core\Lock\MarketplaceLocker::getInstance()->unlock($requestName);

        return $result;
    }

    /**
     * Return list of marketplace request types which are cached in tmp_vars
     *
     * @return array
     */
    protected function getCachedRequestTypes()
    {
        return [
            Constant::REQUEST_UPDATES,
            Constant::REQUEST_CHECK_ADDON_KEY,
            Constant::REQUEST_CORES,
            Constant::REQUEST_ADDONS,
            Constant::REQUEST_TAGS,
            Constant::REQUEST_LANDING,
            Constant::REQUEST_WAVES,
            Constant::INACTIVE_KEYS,
            Constant::REQUEST_PAYMENT_METHODS,
            Constant::REQUEST_SHIPPING_METHODS,
        ];
    }

    /**
     * Get all marketplace actions list
     *
     * @return array
     */
    protected function getMarketplaceActions()
    {
        return [
            Constant::REQUEST_UPDATES          => static::TTL_LONG,
            Constant::REQUEST_CHECK_ADDON_KEY  => static::TTL_LONG,
            Constant::REQUEST_CORES            => static::TTL_LONG,
            Constant::REQUEST_ADDONS           => static::TTL_LONG,
            Constant::REQUEST_TAGS             => static::TTL_LONG,
            Constant::REQUEST_LANDING          => static::TTL_LONG,
            Constant::REQUEST_WAVES            => static::TTL_LONG,
            Constant::REQUEST_PAYMENT_METHODS  => static::TTL_LONG,
            Constant::REQUEST_SHIPPING_METHODS => static::TTL_LONG,

            Constant::REQUEST_INSTALLATION_DATA => static::TTL_NOT_EXPIRED,
        ];
    }

    /**
     * Get action TTL
     *
     * @param string $action Action type
     *
     * @return integer
     */
    protected function getActionTTL($action)
    {
        $ttls = $this->getMarketplaceActions();

        return $ttls[$action] ?? null;
    }

    /**
     * Return action cache variables
     *
     * @param string $action Marketplace action
     *
     * @return array
     */
    protected function getActionCacheVars($action)
    {
        return [
            $action . 'TTL',
            $action . 'Data',
        ];
    }

    /**
     * Save result in the cache
     *
     * @param string  $action        Action type
     * @param mixed   $result        Result
     * @param boolean $saveInTmpVars Flag: true - save result in cache, false - save only timestamp or request
     */
    protected function saveResultInCache($action, $result, $saveInTmpVars)
    {
        [$cellTTL, $cellData] = $this->getActionCacheVars($action);

        if ($saveInTmpVars) {
            // Save in DB (if needed)
            \XLite\Core\TmpVars::getInstance()->$cellData = $result;
        }

        $this->removeScheduledAction($action);
        $this->setTTLStart($cellTTL);
    }

    /**
     * Schedule action
     *
     * @param string $action Action type
     * @param array  $data   Action data
     *
     * @return void
     */
    protected function scheduleAction($action, $data)
    {
        $current = \XLite\Core\TmpVars::getInstance()->marketplaceSchedule;

        if (!$current) {
            $current = [];
        }

        $current[$action] = $data;

        \XLite\Core\TmpVars::getInstance()->marketplaceSchedule = $current;
    }

    /**
     * Remove action from the scheduled actions list
     *
     * @param string $action Action type
     *
     * @return void
     */
    protected function removeScheduledAction($action)
    {
        $current = \XLite\Core\TmpVars::getInstance()->marketplaceSchedule;

        if (isset($current[$action])) {
            unset($current[$action]);
            \XLite\Core\TmpVars::getInstance()->marketplaceSchedule = $current ?: null;
        }
    }

    /**
     * Get list of scheduled actions
     *
     * @return array
     */
    protected function getScheduledActions()
    {
        return \XLite\Core\TmpVars::getInstance()->marketplaceSchedule;
    }

    /**
     * Clear list of scheduled actions
     *
     * @return void
     */
    protected function clearScheduledActions()
    {
        \XLite\Core\TmpVars::getInstance()->marketplaceSchedule = null;
    }

    /**
     * Check and update cache TTL
     *
     * @param string  $cell Name of the cache cell
     * @param integer $ttl  TTL value (in seconds)
     *
     * @return boolean
     */
    protected function checkTTL($cell, $ttl)
    {
        if ($ttl === static::TTL_NOT_EXPIRED) {
            return true;
        }

        // Fetch a certain cell value
        $start = \XLite\Core\TmpVars::getInstance()->$cell;

        return isset($start) && \XLite\Core\Converter::time() < ($start + $ttl);
    }

    // }}}

    // {{{ License check

    /**
     * Renew TTL cell value
     *
     * @param string $cell Name of the cache cell
     *
     * @return void
     */
    protected function setTTLStart($cell)
    {
        \XLite\Core\TmpVars::getInstance()->$cell = \XLite\Core\Converter::time();
    }

    // }}}
}
