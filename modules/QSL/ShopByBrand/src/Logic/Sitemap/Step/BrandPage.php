<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Logic\Sitemap\Step;

use Includes\Utils\URLManager;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;

/**
 * Brand page step
 *
 * @Extender\Depend ("CDev\XMLSitemap")
 */
class BrandPage extends \CDev\XMLSitemap\Logic\Sitemap\Step\ASitemapStep
{
    /**
     * Check if simplified clean url building applicable
     *
     * @return bool
     */
    public static function isSitemapCleanUrlConditionApplicable()
    {
        return LC_USE_CLEAN_URLS;
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('QSL\ShopByBrand\Model\Brand');
    }

    /**
     * Process item
     *
     * @param mixed $brand
     */
    protected function processItem($brand)
    {
        // $brand = $brand[0];
        $brandId = is_object($brand) ? $brand->getBrandId() : null;

        // Iterators can't handle complex joins that we need to drop out brands
        // which have no products (or have no in-stock products). Therefore we
        // have to use this workaround and filter brands in this method.
        $filtered = (Config::getInstance()->General->show_out_of_stock_products === 'directLink') && !$brand->getProducts(null, true);

        if ($brandId && !$filtered) {
            $cleanUrl = $brand->getCleanUrl();
            if ($cleanUrl && static::isSitemapCleanUrlConditionApplicable()) {
                $_url = $cleanUrl;
            } else {
                $_url = Converter::buildURL('brand', '', ['brand_id' => $brandId], \XLite::getCustomerScript(), true);
            }
            $url = \XLite::getInstance()->getShopURL($_url);

            $result = [
                'loc'        => $url,
                'lastmod'    => Converter::time(),
                'changefreq' => Config::getInstance()->CDev->XMLSitemap->brandpage_changefreq,
                'priority'   => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->brandpage_priority),
            ];

            if ($this->generator->hasAlternateLangUrls()) {
                if ($this->languageCode) {
                    $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $_url);
                }

                foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                    $langUrl = $_url;
                    $langUrl = $code . '/' . $langUrl;
                    $locale  = Converter::langToLocale($code);

                    $tag          = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . URLManager::getShopURL($langUrl) . '"';
                    $result[$tag] = null;
                }

                $tag          = 'xhtml:link rel="alternate" hreflang="x-default" href="' . $url . '"';
                $result[$tag] = null;
            }

            $this->generator->addToRecord($result);
        }
    }
}
