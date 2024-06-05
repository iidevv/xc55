<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Controller\Admin;

use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use XLite\Core\Database;

/**
 * Class select couriers
 */
class SelectCouriers extends \XLite\Controller\Admin\AAdmin
{
    public function doNoAction()
    {
        $countPerPage = 20;
        $page = \XLite\Core\Request::getInstance()->page;

        $cnd = new \XLite\Core\CommonCell();
        $cnd->name = \XLite\Core\Request::getInstance()->search;
        $cnd->limit = [($page - 1) * $countPerPage, $countPerPage];
        $cnd->orderBy = ['a.name', 'asc'];

        $couriers = Database::getRepo(AftershipCouriers::class)
            ->search($cnd);
        $result = [];

        if ($couriers) {

            /** @var AftershipCouriers $courier */
            foreach ($couriers as $courier) {
                $result['couriers'][] = [
                    'id' => $courier->getSlug(),
                    'text' => $courier->getName(),
                ];
            }
        }

        $result['more'] = Database::getRepo(AftershipCouriers::class)
                ->search($cnd, 'count') > $page * $countPerPage;

        $this->printAjax($result);
        die();
    }
}
