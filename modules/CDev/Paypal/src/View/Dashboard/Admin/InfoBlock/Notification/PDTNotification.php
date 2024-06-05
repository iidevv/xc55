<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Dashboard\Admin\InfoBlock\Notification;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\TmpVars;
use XLite\Model\Payment\Method;
use CDev\Paypal\Main as PaypalMain;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="200", zone="admin")
 */
class PDTNotification extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'CDevPaypalPDTNotification';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' cdev-paypal-pdt-notification';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Use of the unsupported Payment Data Transfer (PDT) method has been detected');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL(
            'paypal_settings',
            '',
            [
                'method_id' => $this->getPaypalWPSMethod()->getMethodId(),
            ]
        );
    }

    /**
     * @return Method
     */
    protected function getPaypalWPSMethod()
    {
        return PaypalMain::getPaymentMethod('PaypalWPS');
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && TmpVars::getInstance()->CDevPaypalPDTNotificationVisible
            && $this->getPaypalWPSMethod()
            && $this->getPaypalWPSMethod()->getAdded();
    }
}
