<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Clean URL repository
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\Repo\CleanURL
{
    public const SALE_DISCOUNT_URL_FORMAT_NO_EXT = 'domain/goaldiscount';
    public const SALE_DISCOUNT_URL_FORMAT_EXT = 'domain/goaldiscount.html';

    /**
     * Returns 'sale_discount_clean_urls_format' option value
     *
     * @return string
     */
    public static function getSaleDiscountCleanUrlFormat()
    {
        $formats = \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'formats']);
        $format = $formats['sale_discount'];

        return in_array($format, [
            static::SALE_DISCOUNT_URL_FORMAT_EXT,
            static::SALE_DISCOUNT_URL_FORMAT_NO_EXT
        ])
            ? $format
            : static::SALE_DISCOUNT_URL_FORMAT_NO_EXT;
    }

    /**
     * Is use extension for sale discounts
     *
     * @return boolean
     */
    public static function isSaleDiscountUrlHasExt()
    {
        return static::getSaleDiscountCleanUrlFormat() === static::SALE_DISCOUNT_URL_FORMAT_EXT;
    }

    /**
     * Returns available entities types
     *
     * @return array
     */
    public static function getEntityTypes()
    {
        $list = parent::getEntityTypes();
        $list['CDev\Sale\Model\SaleDiscount'] = 'sale_discount';

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
    protected function postProcessURLSaleDiscount($url, $entity, $ignoreExtension = false)
    {
        return $url . (static::isSaleDiscountUrlHasExt() && !$ignoreExtension ? '.' . static::CLEAN_URL_DEFAULT_EXTENSION : '');
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
    protected function parseURLSaleDiscount($url, $last = '', $rest = '', $ext = '')
    {
        $result = $this->findByURL('sale_discount', $url . $ext);

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

        if ($target == 'sale_discount' && !empty($last)) {
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
    protected function buildURLSaleDiscount($params)
    {
        $urlParts = [];

        if (!empty($params['id'])) {
            /** @var \CDev\Sale\Model\SaleDiscount $discount */
            $discount = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')->find($params['id']);

            if (isset($discount) && $discount->getCleanURL()) {
                $urlParts[] = $discount->getCleanURL();
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
    protected function buildFakeURLSaleDiscount($entity, $params, $ignoreExtension = false)
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
        return parent::findCategoryConflictWithOtherTypes($cleanURL) ?: $this->findEntityByURL('sale_discount', $cleanURL);
    }
}
