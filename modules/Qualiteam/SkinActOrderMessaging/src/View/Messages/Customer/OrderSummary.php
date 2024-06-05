<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\Messages\Customer;


class OrderSummary extends \XLite\View\AView
{

    /**
     * Widget parameter names
     */
    public const PARAM_ORDER = 'order';

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                'XLite\Model\Order'
            ),
        ];
    }


    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/order_messages/style.less';

        return $list;
    }


    /**
      * @inheritdoc
      */
     protected function getDefaultTemplate()
     {
         return 'modules/Qualiteam/SkinActOrderMessaging/order_messages/order_summary.twig';
     }





}