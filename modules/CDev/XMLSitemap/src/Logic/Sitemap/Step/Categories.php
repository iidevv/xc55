<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\Logic\Sitemap\Step;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Router;

/**
 * Categories step
 */
class Categories extends \CDev\XMLSitemap\Logic\Sitemap\Step\ASitemapStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('XLite\Model\Category');
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
        $categoryId = $item['category_id'] ?? null;

        if ($categoryId) {
            $category = $this->getRepository()->find($categoryId);
            if (!$category->isVisible()) {
                return;
            }

            if (static::isSitemapCleanUrlConditionApplicable()) {
                $_url = \XLite\Core\Database::getRepo(\XLite\Model\CleanURL::class)->buildCategoryCleanUrl($categoryId);
            } else {
                $_url = Converter::buildURL('category', '', ['category_id' => $categoryId], \XLite::getCustomerScript(), true);
            }

            if (LC_USE_CLEAN_URLS && !preg_match('/\.html$/', $_url)) {
                $_url = rtrim($_url, '/') . '/';
            }

            $url = \XLite::getInstance()->getShopURL($_url);

            $result = [
                'loc' => $url,
                'lastmod' => Converter::time(),
                'changefreq' => Config::getInstance()->CDev->XMLSitemap->category_changefreq,
                'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->category_priority),
            ];

            if ($this->generator->hasAlternateLangUrls()) {
                if ($this->languageCode) {
                    $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $_url);
                }

                foreach (Router::getInstance()->getActiveLanguagesCodes() as $code) {
                    $langUrl = $_url;
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
