<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Clean URL repository
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\Repo\CleanURL
{
    public const NEWS_URL_FORMAT_NO_EXT = 'domain/goalnews';
    public const NEWS_URL_FORMAT_EXT = 'domain/goalnews.html';

    /**
     * Returns 'news_clean_urls_format' option value
     *
     * @return string
     */
    public static function getNewsCleanUrlFormat()
    {
        $formats = \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'formats']);
        $format = $formats['news'];

        return in_array($format, [
            static::NEWS_URL_FORMAT_EXT,
            static::NEWS_URL_FORMAT_NO_EXT
        ])
            ? $format
            : static::NEWS_URL_FORMAT_NO_EXT;
    }

    /**
     * Is use extension for news
     *
     * @return boolean
     */
    public static function isNewsUrlHasExt()
    {
        return static::getNewsCleanUrlFormat() === static::NEWS_URL_FORMAT_EXT;
    }

    /**
     * Returns available entities types
     *
     * @return array
     */
    public static function getEntityTypes()
    {
        $list = parent::getEntityTypes();
        $list['XC\News\Model\NewsMessage'] = 'newsMessage';

        return $list;
    }

    /**
     * Post process clean URL
     *
     * @param string $url URL
     * @param \XLite\Model\Base\Catalog $entity Entity
     *
     * @return string
     */
    protected function postProcessURLNewsMessage($url, $entity, $ignoreExtension = false)
    {
        return $url . ($this->isNewsUrlHasExt() && !$ignoreExtension ? '.' . static::CLEAN_URL_DEFAULT_EXTENSION : '');
    }

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLNewsMessage($url, $last = '', $rest = '', $ext = '')
    {
        $result = $this->findByURL('newsMessage', $url . $ext);

        return $result;
    }

    /**
     * Hook for modules
     *
     * @param string $url Main part of a clean URL
     * @param string $last First part before the "url"
     * @param string $rest Part before the "url" and "last"
     * @param string $ext Extension
     * @param string $target Target
     * @param array $params Additional params
     *
     * @return array
     */
    protected function prepareParseURL($url, $last, $rest, $ext, $target, $params)
    {
        [$target, $params] = parent::prepareParseURL($url, $last, $rest, $ext, $target, $params);

        if ($target == 'newsMessage' && !empty($last)) {
            unset($params['id']);
        }

        return [$target, $params];
    }

    /**
     * Build product URL
     *
     * @param array $params Params
     *
     * @return array
     */
    protected function buildURLNewsMessage($params)
    {
        $urlParts = [];

        if (!empty($params['id'])) {
            /** @var \XC\News\Model\NewsMessage $newsMessage */
            $newsMessage = \XLite\Core\Database::getRepo('XC\News\Model\NewsMessage')->find($params['id']);

            if (isset($newsMessage) && $newsMessage->getCleanURL()) {
                $urlParts[] = $newsMessage->getCleanURL();
                unset($params['id']);
            }
        }

        return [$urlParts, $params];
    }

    /**
     * Build fake url with placeholder
     *
     * @param \XLite\Model\AEntity|string $entity Entity
     * @param array $params Params
     * @param boolean                     $ignoreExtension Ignore default extension
     *
     * @return array
     */
    protected function buildFakeURLNewsMessage($entity, $params, $ignoreExtension)
    {
        $urlParts = [$this->postProcessURL(static::PLACEHOLDER, $entity, $ignoreExtension)];

        return [$urlParts, $params];
    }

    /**
     * @param string $cleanURL
     * @return \XLite\Model\Base\Catalog
     */
    protected function findCategoryConflictWithOtherTypes($cleanURL)
    {
        return parent::findCategoryConflictWithOtherTypes($cleanURL) ?: $this->findEntityByURL('newsMessage', $cleanURL);
    }
}
