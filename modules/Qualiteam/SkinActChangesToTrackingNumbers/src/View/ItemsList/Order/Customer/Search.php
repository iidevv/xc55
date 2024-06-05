<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActChangesToTrackingNumbers\View\ItemsList\Order\Customer;


class Search extends \XLite\View\ItemsList\Order\Customer\Search
{
    protected function getPageBodyTemplate()
    {
        return 'modules/Qualiteam/SkinActChangesToTrackingNumbers/parcels_body.twig';
    }

    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{\XLite\Model\Repo\Order::P_SHIPPING_STATUS} = \XLite\Model\Order\Status\Shipping::STATUS_SHIPPED;

        return parent::getData($cnd, $countOnly);
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActCreateOrder/parcels.css';

        return $list;
    }

}