<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\View\FormField\Select\ShowCardInfoOnInvoicePage;

/**
 * Invoice page
 *
 * @Extender\Mixin
 */
abstract class Invoice extends \XLite\View\Invoice implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/order/invoice/style.css';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list['css'][] = 'modules/XPay/XPaymentsCloud/account/cc_type_sprites.css';
        $list['css'][] = 'modules/XPay/XPaymentsCloud/account/xpayments_cards.less';
        $list['css'][] = 'modules/XPay/XPaymentsCloud/invoice/style.css';

        return $list;
    }

    /**
     * Is next payment date available for current order
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsNextPaymentDateAvailable(\XLite\Model\OrderItem $item)
    {
        return $item->isXpaymentsNextPaymentDateAvailable();
    }

    /**
     * Is last payment failed for current subscription
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentFailed(\XLite\Model\OrderItem $item)
    {
        /** @var \XPay\XPaymentsCloud\Model\Subscription\Subscription $subscription */
        $subscription = $item->getXpaymentsSubscription();

        return $subscription
            && $subscription->getActualDate() > $subscription->getPlannedDate();
    }

    /**
     * Get next payment date
     *
     * @param \XPay\XPaymentsCloud\Model\Subscription\Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextPaymentDate($subscription)
    {
        return $subscription->getPlannedDate();
    }

    /**
     * Get next attempt date
     *
     * @param \XPay\XPaymentsCloud\Model\Subscription\Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextAttemptDate($subscription)
    {
        return $subscription->getActualDate();
    }

    /**
     * @return bool
     */
    protected function isXpaymentsCardInfoVisible()
    {
        switch (\Xlite\Core\Config::getInstance()->XPay->XPaymentsCloud->show_card_info_on_invoice_page) {
            case ShowCardInfoOnInvoicePage::SHOW_TO_EVERYONE:
                $result = true;
                break;
            case ShowCardInfoOnInvoicePage::SHOW_TO_ADMIN_ONLY:
                $result = \XLite\Core\Auth::getInstance()->isAdmin();
                break;
            case ShowCardInfoOnInvoicePage::DO_NOT_SHOW:
                $result = false;
                break;
        }

        return $result;
    }
}
