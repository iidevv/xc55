<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\FormField\Input\Text;

use XLite\Core\Converter;

class OrderAutocomplete extends \XLite\View\FormField\Input\Text\Base\Autocomplete
{
    /**
     * Get dictionary name
     *
     * @return string
     */
    protected function getDictionary()
    {
        return 'order_messaging_orders';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/form_field/order_autocomplete.js';

        return $list;
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        $orders = [];

        if ($profile) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Order::P_PROFILE_ID} =
                \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
            $orders = \XLite\Core\Database::getRepo('\XLite\Model\Order')
                ->search($cnd);
        }

        return parent::getCommentedData() + [
                'ordersData' => $this->packOrdersData($orders),
            ];
    }

    /**
     * Get certain data from profile array for new array
     *
     * @param array $profiles Array of profiles
     *
     * @return array
     */
    protected function packOrdersData(array $orders)
    {
        $result = [];

        if ($orders) {
            foreach ($orders as $k => $order) {
                $result[] = [
                    'label' => $order->getOrderNumber() . ' - ' . Converter::getInstance()->formatTime($order->getDate()),
                    'value' => $order->getOrderNumber()
                ];
            }
        }


        return $result;
    }
}