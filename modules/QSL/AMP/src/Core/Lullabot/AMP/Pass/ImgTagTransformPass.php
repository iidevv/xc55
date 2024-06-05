<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core\Lullabot\AMP\Pass;

use Includes\Utils\URLManager;
use Symfony\Component\Filesystem\Filesystem;
use Lullabot\AMP\Validate\Scope;

/**
 * Override AmpImgFixPass to convert relative image URLs to absolute
 */
class ImgTagTransformPass extends \Lullabot\AMP\Pass\ImgTagTransformPass
{
    /**
     * Override base pass function to convert svg images and drop any images not passing the conversion
     */
    // phpcs:ignore Squiz.Scope.MethodScope.Missing
    function pass()
    {
        // Always make sure we do this. Somewhat of a hack
        if ($this->context->getErrorScope() == Scope::HTML_SCOPE) {
            $this->q->find('html')->attr('amp', '');
        }

        $all_a = $this->q->top()->find('img:not(noscript img)');
        /** @var DOMQuery $el */
        foreach ($all_a as $el) {
            /** @var \DOMElement $dom_el */
            $dom_el = $el->get(0);

            $lineno               = $this->getLineNo($dom_el);
            $context_string       = $this->getContextString($dom_el);
            $has_height_and_width = $this->setResponsiveImgHeightAndWidth($el);
            if (!$has_height_and_width) {
                $el->remove();
                continue;
            }
            if ($this->isPixel($el)) {
                $new_dom_el = $this->convertAmpPixel($el, $lineno, $context_string);
            } elseif (!empty($this->options['use_amp_anim_tag']) && $this->isAnimatedImg($dom_el)) {
                $new_dom_el = $this->convertAmpAnim($el, $lineno, $context_string);
            } else {
                $new_dom_el = $this->convertAmpImg($el, $lineno, $context_string);
            }
            $this->context->addLineAssociation($new_dom_el, $lineno);
            $el->remove(); // remove the old img tag
        }

        return $this->transformations;
    }

    /**
     * Given an image element returns an amp-img element with the same attributes and children
     *
     * @param DOMQuery $el
     * @param int      $lineno
     * @param string   $context_string
     *
     * @return DOMElement
     */
    protected function convertAmpImg($el, $lineno, $context_string)
    {
        $newEl = parent::convertAmpImg($el, $lineno, $context_string);

        $src = trim($newEl->getAttribute('src'));

        $fs = new Filesystem();

        if (!$fs->isAbsolutePath($src)) {
            $absoluteUrl = URLManager::getShopURL($src, null, [], null, null, true);

            $newEl->setAttribute('src', $absoluteUrl);
        }

        return $newEl;
    }

    /**
     * Get SVG image dimensions as Lullabot doesn't do it by itself
     *
     * @param string $src
     * @return bool|array
     */
    protected function getImageWidthHeight($src)
    {
        $dimensions = false;

        $isSvgExtension = preg_match('/.*\.svg$/i', $src);

        if (!$isSvgExtension) {
            $dimensions = parent::getImageWidthHeight($src);
        }

        if (!$dimensions) {
            if (
                $isSvgExtension
                || strpos($this->getUrlContentType($src), 'image/svg+xml') === 0
            ) {
                $dimensions = $this->getSVGImageWidthHeight($src);
            }
        }

        return $dimensions;
    }

    /**
     * @param $src
     * @return array|bool
     */
    protected function getSVGImageWidthHeight($src)
    {
        $dimensions = false;

        $xml = simplexml_load_file($src);

        if ($xml) {
            $width  = isset($xml['width']) ? (string)$xml['width'] : null;
            $height = isset($xml['height']) ? (string)$xml['height'] : null;

            if ($width && $height) {
                $dimensions = [
                    'width'  => $this->fixDimensionUnits($width),
                    'height' => $this->fixDimensionUnits($height),
                ];
            }
        }

        return $dimensions;
    }

    /**
     * Get remote file Content-Type using HEAD request
     *
     * @param $url
     *
     * @return null
     */
    protected function getUrlContentType($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_exec($ch);

        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);

        return $contentType;
    }

    /**
     * AMP does not support "ex" units, convert them to "em"
     *
     * @param $value
     *
     * @return string
     */
    protected function fixDimensionUnits($value)
    {
        // Actual ratio is different for various fonts, use Open Sans's ratio
        $exEmRatio = 0.54;

        if (substr($value, -2) === 'ex') {
            $em = substr($value, 0, -2) * $exEmRatio;

            return $em . 'em';
        }

        return $value;
    }
}
