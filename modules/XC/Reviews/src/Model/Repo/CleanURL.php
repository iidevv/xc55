<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Clean URL repository
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\Repo\CleanURL
{
    public const REVIEWS_PREFIX = 'reviews-';

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLProduct($url, $last = '', $rest = '', $ext = '')
    {
        $result = parent::parseURLProduct($url, $last, $rest, $ext);

        if (empty($result) && strpos($url, static::REVIEWS_PREFIX) === 0) {
            $url = preg_replace('/^' . preg_quote(static::REVIEWS_PREFIX) . '/', '', $url);
            $result = parent::parseURLProduct($url, $last, $rest, $ext);

            if ($result) {
                $result[0] = 'product_reviews';
            }
        }

        return $result;
    }

    /**
     * Hook for modules
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
        [$newTarget, $params] = parent::prepareParseURL(
            $url,
            $last,
            $rest,
            $ext,
            $target == 'product_reviews' ? 'product' : $target,
            $params
        );

        return [$target == 'product_reviews' ? $target : $newTarget, $params];
    }

    /**
     * Build product URL
     *
     * @param array  $params Params
     *
     * @return array
     */
    protected function buildURLProductReviews($params)
    {
        [$urlParts, $params] = $this->buildURLProduct($params);

        if (!empty($urlParts)) {
            $urlParts[0] = static::REVIEWS_PREFIX . $urlParts[0];
        }

        return [$urlParts, $params];
    }

    /**
     * Hook for modules
     *
     * @param string $target   Target
     * @param array  $params   Params
     * @param array  $urlParts URL parts
     *
     * @return array
     */
    protected function prepareBuildURL($target, $params, $urlParts)
    {
        return parent::prepareBuildURL(
            $target == 'product_reviews' ? 'product' : $target,
            $params,
            $urlParts
        );
    }
}
