<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XLite\View\Button\Payment\AddMethod;
use XLite\Model\Payment\Method;

/**
 * Tabs related to translations section
 */
class AddPaymentMethod extends \XLite\View\Tabs\AJsTabs
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'add_online_payment_method'  => [
                'weight'   => 100,
                'title'    => static::t('Online methods'),
                'template' => 'payment/add_method/online.twig',
            ],
            'add_offline_payment_method' => [
                'weight'   => 200,
                'title'    => static::t('Offline methods'),
                'template' => 'payment/add_method/offline.twig',
                'selected' => $this->getPaymentType() === Method::TYPE_OFFLINE,
            ],
        ];
    }

    /**
     * Return payment methods type which is provided to the widget
     *
     * @return string
     */
    protected function getPaymentType()
    {
        return \XLite\Core\Request::getInstance()->{AddMethod::PARAM_PAYMENT_METHOD_TYPE};
    }
}
