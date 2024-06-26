<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Logic;

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
    protected $newsLength;

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
            && $this->position < (parent::count() + $this->getLength())
        ) {
            $data = \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')
                ->findOneAsSitemapLink($this->position - parent::count(), 1);
            $data = $this->assembleNewsMessageData($data);
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
        return parent::count() + $this->getLength();
    }

    /**
     * Get pages length
     *
     * @return integer
     */
    protected function getLength()
    {
        if (!isset($this->newsLength)) {
            $this->newsLength = \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')
                ->countAsSitemapsLinks();
        }

        return $this->newsLength;
    }

    /**
     * Assemble message data
     *
     * @param \XC\News\Model\NewsMessage $newsMessage Message
     *
     * @return array
     */
    protected function assembleNewsMessageData(\XC\News\Model\NewsMessage $newsMessage)
    {
        $_url = Converter::buildURL('news_message', '', ['id' => $newsMessage->getId()], \XLite::getCustomerScript(), false, true);
        $url = static::getShopURL($_url);

        $result = [
            'loc' => $url,
            'lastmod' => Converter::time(),
            'changefreq' => Config::getInstance()->CDev->XMLSitemap->news_changefreq,
            'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->news_priority),
        ];

        if ($this->hasAlternateLangUrls()) {
            if ($this->languageCode) {
                $result['loc'] = static::getShopURL($this->languageCode . '/' . $_url);
            }

            foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $langUrl = $_url;
                $langUrl = $code . '/' . $langUrl;
                $locale = Converter::langToLocale($code);

                $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . htmlspecialchars(static::getShopURL($langUrl)) . '"';
                $result[$tag] = null;
            }

            $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($url) . '"';
            $result[$tag] = null;
        }
        return $result;
    }
}
