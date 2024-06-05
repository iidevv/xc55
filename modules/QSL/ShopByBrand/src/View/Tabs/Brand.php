<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;

/**
 * Tabs related to category section
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Brand extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'brand';
        $list[] = 'brand_products';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'brand' => [
                'weight'   => 100,
                'title'    => static::t('Info'),
                'template' => 'modules/QSL/ShopByBrand/brand/body.twig'
            ],
            'brand_products' => [
                'weight'   => 200,
                'title'    => static::t('Products'),
                'template' => 'modules/QSL/ShopByBrand/brand_products/body.twig',
            ]
        ];
    }

    /**
     * @return int
     */
    protected function getBrandId()
    {
        $request = Request::getInstance();
        $target = (string)$request->target;
        $result = 0;
        if ($target === 'brand') {
            $result = (int) $request->brand_id;
        } elseif ($target === 'brand_products') {
            $result = (int) $request->id;
        }
        return $result;
    }

    /**
     * @param string $target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        $brandId = $this->getBrandId();
        $params = [];
        if ($brandId) {
            if ($target === 'brand') {
                $params['brand_id'] = $brandId;
            } elseif ($target === 'brand_products') {
                $params['id'] = $brandId;
            }
        }
        return $this->buildURL($target, '', $params);
    }
}
