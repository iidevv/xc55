<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Class PixelContentData
 *
 * @ListChild (list="center.top", weight="10")
 */
class PixelContentData extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FacebookMarketing/pixel_content_data/body.twig';
    }

    protected function getPixelData()
    {
        $pixelData = [];
        $target = \XLite::getController()->getTarget();
        switch (true) {
            case ($target === 'main'):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Home page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ($target === 'category'):
                $pixelData = [
                    'type' => 'category',
                    'data' => [
                        'content_name' => $this->getCategory()->getName(),
                        'content_category' => implode(
                            ' > ',
                            array_map(
                                static function ($category) {
                                    return $category->getName();
                                },
                                $this->getCategory()->getPath()
                            )
                        ),
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ($target === 'sale_products'):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Sale page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ($target === 'coming_soon'):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Coming soon page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ($target === 'new_arrivals'):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'New arrivals page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
            case ($target === 'bestsellers'):
                $pixelData = [
                    'type' => 'content',
                    'data' => [
                        'content_name' => 'Bestsellers page',
                        'content_type' => 'product',
                        'content_ids'  => [],
                    ]
                ];
                break;
        }

        return $pixelData;
    }
}
