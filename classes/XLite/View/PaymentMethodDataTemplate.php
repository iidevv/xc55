<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Payment method data template widget
 */
class PaymentMethodDataTemplate extends \XLite\View\AView
{
    /**
     * Cached processor
     *
     * @var \XLite\Model\Payment\Base\Processor
     */
    protected $processor = null;

    /**
     * Display template
     *
     * @param string $template Template path OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        // Use customer layout to display template
        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        $layout->callInInterfaceZone(function () {
            parent::display();
        }, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER);
    }

    /**
     * Get payment processor
     *
     * @return \XLite\Model\Payment\Base\Processor
     */
    protected function getProcessor()
    {
        if (!isset($this->processor)) {
            $transactionId = \XLite\Core\Request::getInstance()->transaction_id;

            $transaction = $transactionId
                ? \XLite\Core\Database::getRepo('\XLite\Model\Payment\Transaction')->find($transactionId)
                : null;

            $this->processor = $transaction && $transaction->getPaymentMethod()
                ? $transaction->getPaymentMethod()->getProcessor()
                : null;

            if (!$this->processor) {
                $this->processor = false;
            }
        }

        return $this->processor;
    }

    /**
     * Get payment template
     *
     * @return string|void
     */
    protected function getDefaultTemplate()
    {
        return $this->getProcessor() ? $this->getProcessor()->getInputTemplate() : null;
    }
}
