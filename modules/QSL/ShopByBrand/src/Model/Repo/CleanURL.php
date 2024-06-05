<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Clean URL repository
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\Repo\CleanURL
{
    public const BRAND_FORMAT_EXT    = 'domain/goalbrand.html';
    public const BRAND_FORMAT_NO_EXT = 'domain/goalbrand';

    public static function getBrandCleanUrlsFormat()
    {
        $formats = \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'formats']);

        return $formats['brand'] ?: static::BRAND_FORMAT_NO_EXT;
    }

    /**
     * Is use extension for level url
     *
     * @return boolean
     */
    public static function isBrandUrlHasExt()
    {
        return static::getBrandCleanUrlsFormat() === static::BRAND_FORMAT_EXT;
    }

    /**
     * Returns code names for entity classes having clean URLs.
     *
     * @return array
     */
    public static function getEntityTypes()
    {
        $list = parent::getEntityTypes();

        $list['QSL\ShopByBrand\Model\Brand'] = 'brand';

        return $list;
    }

    /**
     * Post process clean URL for brand page.
     *
     * @param string               $url             URL
     * @param \XLite\Model\AEntity $entity          Entity
     * @param boolean              $ignoreExtension Ignore default extension
     *
     * @return string
     */
    protected function postProcessURLBrand($url, $entity, $ignoreExtension = false)
    {
        $result = $url;

        if (!$ignoreExtension && static::isBrandUrlHasExt()) {
            $result .= '.' . static::CLEAN_URL_DEFAULT_EXTENSION;
        }

        return $result;
    }

    /**
     * Parse clean URL for brand pages.
     * Returns array((string) $target, (array) $params).
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLBrand($url, $last = '', $rest = '', $ext = '')
    {
        return $this->findByURL('brand', $url . $ext);
    }

    /**
     * Retrieves the brand-page target and parameters from the given URL.
     *
     * @param string $url    Main part of a clean URL
     * @param string $last   First part before the "url"
     * @param string $rest   Part before the "url" and "last"
     * @param string $ext    Extension
     * @param string $target Target
     * @param array  $params Additional params
     *
     * @return array
     */
    protected function prepareParseURL($url, $last, $rest, $ext, $target, $params)
    {
        [$target, $params] = parent::prepareParseURL($url, $last, $rest, $ext, $target, $params);

        if ($target === 'brand' && !empty($last)) {
            unset($params['brand_id']);
        }

        return [$target, $params];
    }

    /**
     * Build brand page URL.
     *
     * @param array $params Params
     *
     * @return array
     */
    protected function buildURLBrand($params)
    {
        $urlParts = [];

        if (!empty($params['brand_id'])) {
            /** @var \QSL\ShopByBrand\Model\Brand $brand */
            $brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->find($params['brand_id']);

            if (isset($brand) && $brand->getCleanURL()) {
                $urlParts[] = $brand->getCleanURL();
                unset($params['brand_id']);
            }
        }

        return [$urlParts, $params];
    }

    /**
     * Build fake url with placeholder
     *
     * @param \XLite\Model\AEntity|string $entity          Entity
     * @param array                       $params          Params
     * @param boolean                     $ignoreExtension Ignore default extension
     *
     * @return string
     */
    protected function buildFakeURLBrand($entity, $params, $ignoreExtension = false)
    {
        $urlParts = [$this->postProcessURL(static::PLACEHOLDER, $entity, $ignoreExtension)];

        return [$urlParts, $params];
    }

    /**
     * @param string $cleanURL
     *
     * @return \XLite\Model\Base\Catalog
     */
    protected function findCategoryConflictWithOtherTypes($cleanURL)
    {
        return parent::findCategoryConflictWithOtherTypes($cleanURL) ?: $this->findEntityByURL('brand', $cleanURL);
    }
}
