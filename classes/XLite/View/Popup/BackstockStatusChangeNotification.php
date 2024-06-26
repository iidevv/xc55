<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Popup;

class BackstockStatusChangeNotification extends \XLite\View\AView
{
    public const PARAM_ORDER = 'order';

    protected function getDefaultTemplate()
    {
        return 'popup/backstock_status_change_notification/body.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'popup/backstock_status_change_notification/style.css',
        ]);
    }

    /**
     * Define widget params
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                '\XLite\Model\Order'
            ),
        ];
    }
}
