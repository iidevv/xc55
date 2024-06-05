<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\View\ItemsList\Model\Order\Admin;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search
{

    /**
     * Widget param names
     */
    public const PARAM_VERIFICATION_STATUS = \XLite\Model\Repo\Order::SEARCH_VERIFICATION_STATUS;

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        $list = parent::getSearchParams();
        $list[\XLite\Model\Repo\Order::SEARCH_VERIFICATION_STATUS] = static::PARAM_VERIFICATION_STATUS;

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_VERIFICATION_STATUS => new \XLite\Model\WidgetParam\TypeString('Verification status condition', ''),
        ];
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/OrdersListCustomerMark.js';
        return $list;
    }


    public function displayCommentedData(array $data)
    {
        $data['verifiedStatusId'] = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActVerifiedCustomer->order_verified_status_id;
        parent::displayCommentedData($data);
    }


}