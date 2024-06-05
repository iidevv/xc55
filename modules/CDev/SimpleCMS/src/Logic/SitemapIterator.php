<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Logic;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Converter;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\XMLSitemap")
 */
class SitemapIterator extends \CDev\XMLSitemap\Logic\SitemapIterator
{
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
            && $this->position < (parent::count() + $this->getPagesLength())
        ) {
            $data = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')
                ->findOneAsSitemapLink($this->position - parent::count(), 1);
            $data = $this->assemblePageData($data);
        }

        return $data;
    }

    /**
     * Get length
     *
     * @return integer
     */
    public function count()
    {
        return parent::count() + $this->getPagesLength();
    }

    /**
     * Get pages length
     *
     * @return integer
     */
    protected function getPagesLength()
    {
        if (!isset($this->pagesLength)) {
            $this->pagesLength = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Page')
                ->countPagesAsSitemapsLinks();
        }

        return $this->pagesLength;
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
        $result = [];

        if ($page->isIncludeToSitemap()) {
            if ($page->getFrontUrl()) {
                if (static::isSitemapCleanUrlConditionApplicable()) {
                    $pageCleanURL = $page->getCleanURLbyFrontURL() ?: $page->getFrontUrl();
                } else {
                    $pageCleanURL = $page->getFrontUrl();
                }
            } else {
                if (static::isSitemapCleanUrlConditionApplicable()) {
                    $pageCleanURL = Converter::buildCleanURL('page', '', ['id' => $page->getId()], \XLite::getCustomerScript());
                } else {
                    $pageCleanURL = \Includes\Utils\Converter::buildURL('page', '', ['id' => $page->getId()], \XLite::getCustomerScript());
                }
            }
            $url = static::getShopURL($pageCleanURL);

            $result = [
                'loc' => $url,
                'lastmod' => Converter::time(),
                'changefreq' => Config::getInstance()->CDev->XMLSitemap->page_changefreq,
                'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->page_priority),
            ];

            if ($this->hasAlternateLangUrls()) {
                if ($this->languageCode) {
                    $result['loc'] = static::getShopURL($this->languageCode . '/' . $pageCleanURL);
                }

                foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                    $langUrl = $pageCleanURL;
                    $langUrl = $code . '/' . $langUrl;
                    $locale = Converter::langToLocale($code);

                    $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . htmlspecialchars(static::getShopURL($langUrl)) . '"';
                    $result[$tag] = null;
                }

                $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($url) . '"';
                $result[$tag] = null;
            }
        }
        return $result;
    }

    /**
     * Check if simplified clean url building applicable
     *
     * @return bool
     */
    public static function isSitemapCleanUrlConditionApplicable()
    {
        return LC_USE_CLEAN_URLS;
    }
}
