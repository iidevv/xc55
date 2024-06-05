<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View;

use QSL\CloudSearch\Core\SearchParameters;
use QSL\CloudSearch\Core\ServiceApiClient;
use QSL\CloudSearch\Model\Repo\Product as ProductRepo;
use QSL\CloudSearch\View\CloudFilters\FiltersBox;
use QSL\CloudSearch\View\CloudFilters\FiltersBoxPlaceholder;
use XCart\Extender\Mapping\Extender;
use XLite;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\Layout;

/**
 * Controller widget extension
 *
 * @Extender\Mixin
 */
class Controller extends \XLite\View\Controller
{
    protected static bool $showCloudFilters = false;

    protected static CommonCell $cloudFilterConditions;

    protected static bool $isAsyncCloudFilters;

    /**
     * Called from the outside to initialize FiltersBox
     *
     * @param $filterConditions
     * @param $isAsyncFilters
     */
    public static function showCloudFilters($filterConditions, $isAsyncFilters): void
    {
        self::$showCloudFilters = true;

        self::$cloudFilterConditions = $filterConditions;

        self::$isAsyncCloudFilters = $isAsyncFilters;
    }

    /**
     * Return common data to send to JS
     *
     * @return array
     */
    protected function getCommonJSData()
    {
        $data = parent::getCommonJSData();

        if (!XLite::isAdminZone()) {
            $data += $this->getCloudSearchInitData();
        }

        return $data;
    }

    /**
     * Get CloudSearch initialization data to pass to the JS code
     */
    protected function getCloudSearchInitData(): array
    {
        $lng = [
            'lbl_showing_results_for'  => static::t('cs_showing_results_for'),
            'lbl_see_details'          => static::t('cs_see_details'),
            'lbl_see_more_results_for' => static::t('cs_see_more_results_for'),
            'lbl_suggestions'          => static::t('cs_suggestions'),
            'lbl_products'             => static::t('cs_products'),
            'lbl_categories'           => static::t('cs_categories'),
            'lbl_pages'                => static::t('cs_pages'),
            'lbl_manufacturers'        => static::t('cs_manufacturers'),
            'lbl_did_you_mean'         => static::t('cs_did_you_mean'),
        ];

        $client = new ServiceApiClient();

        $conditions = [
            'availability' => ['Y'],
        ];

        if (Config::getInstance()->General->show_out_of_stock_products === 'directLink') {
            $conditions += [
                'stock_status' => SearchParameters::getStockStatusCondition(ProductRepo::INV_IN),
            ];
        }

        $data = [
            'cloudSearch' => [
                'apiUrl'               => $client->getSearchApiUrl(),
                'apiKey'               => $client->getApiKey(),
                'priceTemplate'        => static::formatPrice(0),
                'selector'             => 'input[name="substring"]',
                'lng'                  => $lng,
                'dynamicPricesEnabled' => $this->isCloudSearchDynamicPricesEnabledCached(),
                'requestData'          => [
                    'membership' => Auth::getInstance()->getMembershipId(),
                    'conditions' => $conditions,
                ],
            ],
        ];

        return $data;
    }

    /**
     * Enable dynamic prices if there are taxes configured
     */
    protected function isCloudSearchDynamicPricesEnabledCached(): bool
    {
        $key = $this->getCloudSearchDynamicPricesEnabledCacheKey();

        $result = $this->getCache()->get($key);

        if ($result === null) {
            $result = $this->isCloudSearchDynamicPricesEnabled();

            $this->getCache()->set($key, $result);
        }

        return $result;
    }

    protected function getCloudSearchDynamicPricesEnabledCacheKey(): array
    {
        $key = [
            'QSL\CloudSearch\View\Controller',
        ];

        $rateRepos = [
            'CDev\VAT\Model\Tax\Rate',
            'CDev\SalesTax\Model\Tax\Rate',
        ];

        foreach ($rateRepos as $rateRepo) {
            $repo = Database::getRepo($rateRepo);

            if ($repo !== null) {
                $key[] = $repo->getVersion();
            }
        }

        return $key;
    }

    /**
     * Enable dynamic prices if there are taxes configured
     */
    protected function isCloudSearchDynamicPricesEnabled(): bool
    {
        $rateRepos = [
            'CDev\VAT\Model\Tax\Rate',
            'CDev\SalesTax\Model\Tax\Rate',
        ];

        foreach ($rateRepos as $rateRepo) {
            $repo = Database::getRepo($rateRepo);

            if ($repo !== null) {
                $rates = $repo->findAll();

                if (count($rates) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Replace FiltersBoxPlaceholder with the actual rendered FiltersBox
     *
     * @return void
     */
    protected function prepareContent()
    {
        parent::prepareContent();

        $pattern = '/' . preg_quote(FiltersBoxPlaceholder::CLOUD_FILTERS_PLACEHOLDER_VALUE) . '/';

        $widgetRendered = false;

        self::$bodyContent = preg_replace_callback(
            $pattern,
            function () use (&$widgetRendered) {
                if (self::$showCloudFilters) {
                    $widget = $this->getChildWidget(
                        'QSL\CloudSearch\View\CloudFilters\FiltersBox',
                        [
                            FiltersBox::PARAM_FILTER_CONDITIONS => self::$cloudFilterConditions,
                            FiltersBox::PARAM_IS_ASYNC_FILTERS  => self::$isAsyncCloudFilters,
                        ]
                    );

                    $content = $widget->getContent();

                    $widgetRendered = !empty($content);

                    return $content;
                } else {
                    return '';
                }
            },
            self::$bodyContent
        );

        $layout = Layout::getInstance();

        if (!$widgetRendered) {
            $content = $layout->getCloudSearchSidebarContent();

            $content = preg_replace($pattern, '', $content);

            if (trim($content) === '') {
                $layout->setSidebarState($layout->getSidebarState() | Layout::SIDEBAR_STATE_FIRST_EMPTY);
            }
        }
    }
}
