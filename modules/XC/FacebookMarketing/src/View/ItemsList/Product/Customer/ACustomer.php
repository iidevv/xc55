<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product list widget
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    protected function getPixelContentLists()
    {
        return [
            'main'          => ['\CDev\FeaturedProducts\View\Customer\FeaturedProducts'],
            'category'      => [
                '\XLite\View\ItemsList\Product\Customer\Category\Main',
            ],
            'sale_products' => ['\CDev\Sale\View\SalePage'],
            'coming_soon'   => ['\CDev\ProductAdvisor\View\ComingSoonPage'],
            'new_arrivals'  => ['\CDev\ProductAdvisor\View\NewArrivalsPage'],
            'bestsellers'   => ['\CDev\Bestsellers\View\BestsellersPage'],
            'search'        => ['\XLite\View\ItemsList\Product\Customer\Search'],
        ];
    }

    protected function getJSData()
    {
        $jsData = parent::getJSData();

        $pixelData = [];
        $target = \XLite::getController()->getTarget();
        $contentLists = $this->getPixelContentLists();
        if (isset($contentLists[$target])) {
            foreach ($contentLists[$target] as $list) {
                if (is_a($this, $list)) {
                    $pixelData['content_ids'] = $this->getFbPixelProductIds();
                    break;
                }
            }
        }

        if ($target == 'search' && is_a($this, '\XLite\View\ItemsList\Product\Customer\Search')) {
            $pixelData['search_string'] = $this->getParam(static::PARAM_SUBSTRING) ?: '';
        }

        if ($pixelData) {
            $jsData['fb_pixel_content_data'] = $pixelData;
        }

        return $jsData;
    }

    protected function getFbPixelProductIds()
    {
        $pageData = $this->getPageData();

        $ids = [];
        foreach (array_slice($pageData, 0, 10) as $product) {
            $ids[] = $product->getFacebookPixelProductIdentifier();
        }

        return $ids;
    }
}
