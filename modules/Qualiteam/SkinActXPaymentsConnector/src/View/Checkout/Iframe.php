<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Checkout;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;

/**
 * iframe
 */
class Iframe extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'checkout';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/iframe.twig';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/checkout';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $pm = $this->getCart()->getPaymentMethod();

        return parent::isVisible()
            && $pm
            && XPayments::class == $pm->getClass()
            && \Qualiteam\SkinActXPaymentsConnector\Core\Iframe::getInstance()->useIframe();
    }

}
