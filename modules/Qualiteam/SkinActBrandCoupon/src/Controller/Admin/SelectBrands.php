<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBrandCoupon\Controller\Admin;

use QSL\ShopByBrand\Model\Brand;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * Class select couriers
 */
class SelectBrands extends \XLite\Controller\Admin\AAdmin
{
    public function doNoAction()
    {
        $countPerPage = 20;
        $page = Request::getInstance()->page;

        $cnd = new CommonCell();
        $cnd->substring = Request::getInstance()->search;
        $cnd->limit = [($page - 1) * $countPerPage, $countPerPage];
        $cnd->orderBy = ['t.name', 'asc'];

        $brands = Database::getRepo(Brand::class)
            ->search($cnd);
        $result = [];
        $result['findingBrands'] = [];

        if ($brands) {

            /** @var Brand $brand */
            foreach ($brands as $brand) {
                $result['findingBrands'][] = [
                    'id' => $brand->getId(),
                    'text' => $brand->getName(),
                ];
            }
        }

        $result['more'] = Database::getRepo(Brand::class)
                ->search($cnd, 'count') > $page * $countPerPage;

        $this->printAjax($result);
        die();
    }
}
