<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
namespace Qualiteam\SkinActXPaymentsConnector\Controller\Customer;

use Qualiteam\SkinActXPaymentsConnector\Core\Iframe;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XLite\Model\Payment\Transaction;

/**
 * Redirect inside iframe 
 * it should redirect the parent window
 *
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get iframe singleton
     *
     * @return Iframe
     */
    public function getIframe()
    {
        return Iframe::getInstance();
    }

    /**
     * Perform redirect
     *
     * @param string $url Redirect URL OPTIONAL
     * @param null   $code
     */
    protected function redirect($url = null, $code = null)
    {
        if ($this->getIframe()->getEnabled()) {
            if ('checkout' == Request::getInstance()->action) {
                if ($this->get('absence_of_product')) {
                    $this->getIframe()->setError('Error initializing payment form. Please try to refresh the page.');
                    $this->getIframe()->setType(Iframe::IFRAME_ALERT);
                } else {
                    $this->getIframe()->setError('');
                    $this->getIframe()->setType(Iframe::IFRAME_DO_NOTHING);
                }
            }
            $this->getIframe()->finalize();
        } else {
            parent::redirect($url, $code);
        }
    }

    /**
     * Check if transaction is X-Payments's one and is initialized/in progress 
     *
     * @param Transaction $transaction Transaction
     *
     * @return bool
     */
    protected function isTransactionOpenXpayments(Transaction $transaction)
    {
        return $transaction
            && $transaction->isXpc()
            && $transaction->isOpen();
    }
}
