<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Logic;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Converter;

/**
 * Sitemap links iterator
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\XMLSitemap")
 */
class SitemapIterator extends \CDev\XMLSitemap\Logic\SitemapIterator
{
    /**
     * Total number of brand pages.
     *
     * @var integer
     */
    protected $brandPagesCount;

    /**
     * Get current data
     *
     * @return array
     */
    public function current()
    {
        $data = parent::current();

        if (
            $this->position >= parent::count()
            && $this->position < (parent::count() + $this->countBrandPages())
        ) {
            $data = $this->assembleBrandPageData(
                \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneAsSitemapLink(
                    $this->position - parent::count(),
                    1
                )
            );
        }

        return $data;
    }

    /**
     * Get length
     *
     * @return int
     */
    public function count()
    {
        return parent::count() + $this->countBrandPages();
    }

    /**
     * Get pages length
     *
     * @return int
     */
    protected function countBrandPages()
    {
        if (!isset($this->brandPagesCount)) {
            $this->brandPagesCount = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')
                ->countEnabledBrands();
        }

        return $this->brandPagesCount;
    }

    /**
     * Assemble page data
     *
     * @param \QSL\ShopByBrand\Model\Brand $brand Brand
     *
     * @return array
     */
    protected function assembleBrandPageData(\QSL\ShopByBrand\Model\Brand $brand)
    {
        $_url = Converter::buildURL('brand', '', ['brand_id' => $brand->getBrandId()], \XLite::getCustomerScript(), true);
        $url  = \XLite::getInstance()->getShopURL($_url);

        $result = [
            'loc'        => $url,
            'lastmod'    => Converter::time(),
            'changefreq' => Config::getInstance()->CDev->XMLSitemap->brandpage_changefreq,
            'priority'   => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->brandpage_priority),
        ];

        if ($this->hasAlternateLangUrls()) {
            if ($this->languageCode) {
                $result['loc'] = \Includes\Utils\URLManager::getShopURL($this->languageCode . '/' . $_url);
            }

            foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $langUrl = $_url;
                $langUrl = $code . '/' . $langUrl;
                $locale  = Converter::langToLocale($code);

                $tag          = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . \Includes\Utils\URLManager::getShopURL($langUrl) . '"';
                $result[$tag] = null;
            }

            $tag          = 'xhtml:link rel="alternate" hreflang="x-default" href="' . $url . '"';
            $result[$tag] = null;
        }

        return $result;
    }

    /**
     * Assemble page data
     *
     * @param \CDev\SimpleCMS\Model\Page $page Page
     *
     * @return array
     */
    protected function assemblePageData(\CDev\SimpleCMS\Model\Page $page)
    {
        // Workaround due to SimpleCMS module executing this method on non-SimpleCMS sitemap items
        return ($page instanceof \CDev\SimpleCMS\Model\Page) ? parent::assemblePageData($page) : null;
    }
}
