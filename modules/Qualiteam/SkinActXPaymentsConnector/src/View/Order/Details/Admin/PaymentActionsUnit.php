<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Order\Details\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;

/**
 * Payment actions unit widget (button capture or refund or void etc)
 *
 * @Extender\Mixin
 */
class PaymentActionsUnit extends \XLite\View\Order\Details\Admin\PaymentActionsUnit
{
    /**
     * Associated payment transaction
     */
    protected $transaction = null;

    /**
     * Get associated payment transaction 
     *
     * @return Transaction
     */
    protected function getTransaction()
    {
        if (!$this->transaction) {
            $this->transaction = $this->getParam(self::PARAM_TRANSACTION);
        }

        return $this->transaction;
    }

    /**
     * Get action type 
     *
     * @return string
     */
    protected function getActionType()
    {
         return $this->getParam(self::PARAM_UNIT);
    }

    /**
     * Check if it's X-Payments transaction
     *
     * @param bool $includeSavedCardsMethod include or not Use saved card transactions
     *
     * @return bool 
     */
    protected function isXpc($includeSavedCardsMethod = false)
    {
        return $this->getTransaction()->isXpc($includeSavedCardsMethod);
    }

    /**
     * Widget default template 
     *
     * @return string 
     */
    protected function getDefaultTemplate()
    {
        // We cannot set parent's template because widget is not initialized yet
        // and thus getParam(self::PARAM_TRANSACTION) returns null
        return 'modules/Qualiteam/SkinActXPaymentsConnector/order/order/payment_actions/unit.twig';
    }

    /** 
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $files = parent::getJSFiles();

        $files[] = 'modules/Qualiteam/SkinActXPaymentsConnector/order/script.js';
        $files[] = 'modules/Qualiteam/SkinActXPaymentsConnector/order/order/payment_actions/jquery.confirm.min.js';

        return $files;
    }

    /**
     * Get CSS class
     *
     * @return string
     */
    protected function getCSSClass()
    {
        $class = parent::getCSSClass();

        if ($this->isDisplayAmountField()) {
            $class .= ' with-input';
        }

        if (
            $this->getTransaction()->isFraudStatus()
            && !$this->getTransaction()->isPendingFraudCheck()
        ) {
            $class .= ' fraud-disabled';
        }
    
        return $class;
    }

    /**
     * Widget (button) displayed name 
     *
     * @return string
     */
    public function getUnitName()
    {
        $name = parent::getUnitName();

        if (
            $this->isDisplayAmount()
            && !$this->isDisplayAmountField()
        ) {

            $name .= ' (' . $this->formatPrice($this->getAmountFieldValue()) . ')';

        }

        return $name;
    }

    /**
     * Display or not amount
     *
     * @return bool
     */
    public function isDisplayAmount()
    {
        $result = false;
        $transaction = $this->getTransaction();

         if (
            $transaction
            && $this->isXpc(true)
        ) {

            $type = $this->getActionType();

            $result = !in_array(
                $type,
                array(
                    BackendTransaction::TRAN_TYPE_ACCEPT,
                    BackendTransaction::TRAN_TYPE_DECLINE,
                )
            );
        }

        return $result;
    }

    /**
     * Display or not amount field for this payment action 
     *
     * @return bool
     */
    public function isDisplayAmountField()
    {
        $result = false;
        $transaction = $this->getTransaction();

        if (
            $transaction
            && $this->isXpc(true)
        ) {
    
            $type = $this->getActionType();

            if (
                (
                    BackendTransaction::TRAN_TYPE_CAPTURE == $type
                    && $transaction->getXpcDataCell('xpc_can_do_capturePart')
                ) || (
                    BackendTransaction::TRAN_TYPE_VOID == $type
                    && $transaction->getXpcDataCell('xpc_can_do_voidPart')
                ) || (
                    BackendTransaction::TRAN_TYPE_REFUND == $type
                    && $transaction->getXpcDataCell('xpc_can_do_refundPart')
                )      
            ) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * DOM id of the amount input box 
     *
     * @return string 
     */
    public function getAmountFieldId()
    {
        return $this->getActionType() . '-amount-' . $this->getTransaction()->getTransactionId();
    }

    /**
     * Payment action amount value 
     *
     * @return float
     */
    public function getAmountFieldValue()
    {
        $value = 0;

        if (
            $this->getTransaction()
            && $this->isXpc(true)
        ) {

            $type = $this->getActionType();
            $transaction = $this->getTransaction();   
 
            if (
                BackendTransaction::TRAN_TYPE_CAPTURE == $type
                && $transaction->getXpcDataCell('xpc_can_capture')
            ) {

                $value = $transaction->getXpcDataCell('xpc_can_capture')->getValue();

            } elseif (
                BackendTransaction::TRAN_TYPE_VOID == $type
                && $transaction->getXpcDataCell('xpc_can_void')
            ) {

                $value = $transaction->getXpcDataCell('xpc_can_void')->getValue();

            } elseif (
                BackendTransaction::TRAN_TYPE_REFUND == $type
                && $transaction->getXpcDataCell('xpc_can_refund')
            ) {

                $value = $transaction->getXpcDataCell('xpc_can_refund')->getValue();
            }
        }

        return number_format($value, 2, '.', '');
    }

    /**
     * Get JS code
     *
     * @return string
     */
    public function getJsCode()
    {
        $operation = '"' . $this->getActionType() . '"';

        $location = '"' . $this->getActionURL() . '"';

        if ($this->isDisplayAmountField()) {
            $jsAmountCode = '$("#' . $this->getAmountFieldId() . '").val()';
            $location .= ' + "&amount=" + ' . $jsAmountCode;
        } else {
            $jsAmountCode = '"' . $this->getAmountFieldValue() . '"';
        }

        $currency = $this->getTransaction()->getOrder()->getCurrency();

        $currencyPrefix = '"' . $currency->getPrefix() . '"';
        $currencySuffix = '"' . $currency->getSuffix() . '"';

        $code = implode(', ', array($operation, $jsAmountCode, $location, $currencyPrefix, $currencySuffix));

        $code = 'confirmOperation(' . $code . ');';

        return $code;
    }

    /**
     * Display widget or not currently
     *
     * @param bool $isTransactionsList Is widget inside transactions list or not
     *
     * @return bool
     */
    protected function displayItNow($isTransactionsList = false)
    {
        return ($isTransactionsList xor $this->isXpc(true));
    }
}
