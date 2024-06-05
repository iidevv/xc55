<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Logic\Sitemap\Step;

use Includes\Utils\URLManager;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;

/**
 * @Extender\Depend ("CDev\XMLSitemap")
 */
class Page extends \CDev\XMLSitemap\Logic\Sitemap\Step\ASitemapStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('CDev\SimpleCMS\Model\Page');
    }

    // }}}

    // {{{ Row processing

    /**
     * Process item
     *
     * @param mixed $item
     */
    protected function processItem($item)
    {
        $pageId = $item['id'] ?? null;

        if ($pageId) {
            $page = $this->getRepository()->find($pageId);

            if ($page && $page->isIncludeToSitemap()) {
                if (isset($item['cleanURL']) && static::isSitemapCleanUrlConditionApplicable()) {
                    $pageCleanURL = $item['cleanURL'];
                } else {
                    if (static::isSitemapCleanUrlConditionApplicable()) {
                        $pageCleanURL = $page->getCleanURLbyFrontURL() ?: $page->getFrontUrl();
                    } else {
                        $pageCleanURL = $page->getFrontUrl();
                    }
                }

                $url = \XLite::getInstance()->getShopURL($pageCleanURL);

                $result = [
                    'loc' => $url,
                    'lastmod' => Converter::time(),
                    'changefreq' => Config::getInstance()->CDev->XMLSitemap->page_changefreq,
                    'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->page_priority),
                ];

                if ($this->generator->hasAlternateLangUrls()) {
                    if ($this->languageCode) {
                        $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $pageCleanURL);
                    }

                    foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                        $langUrl = $pageCleanURL;
                        $langUrl = $code . '/' . $langUrl;
                        $locale = Converter::langToLocale($code);

                        $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . htmlspecialchars(URLManager::getShopURL($langUrl)) . '"';
                        $result[$tag] = null;
                    }

                    $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($url) . '"';
                    $result[$tag] = null;
                }

                $this->generator->addToRecord($result);
            }
        }
    }

    // }}}

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
