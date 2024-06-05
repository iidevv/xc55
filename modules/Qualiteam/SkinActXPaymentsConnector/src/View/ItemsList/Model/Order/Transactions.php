<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\ItemsList\Model\Order;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use Qualiteam\SkinActXPaymentsConnector\Model\Repo\Payment\BackendTransaction;
use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\View\ItemsList\Model\Table;
use XLite\View\Order\Details\Admin\PaymentActions;

/**
 * List of XPC transactions and cards 
 */
class Transactions extends Table
{
    /**
     * Column name
     */
    const COLUMN_NAME = 'transaction';

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            self::COLUMN_NAME => array(
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActXPaymentsConnector/order/transactions/transaction.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_ORDERBY  => 200,
                static::COLUMN_MAIN     => true,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return XpcTransactionData::class;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return string
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }

    // }}}

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array();
    }

    // }}}

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isTableHeaderVisible()
    {
        return false;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $class = BackendTransaction::class;

        if (
            Manager::getRegistry()->isModuleEnabled('XC\MultiVendor')
            && $this->getOrder()->getParent()
        ) {
            $order = $this->getOrder()->getParent();
        } else {
            $order = $this->getOrder();
        }

        $cnd->{$class::SEARCH_ORDER_ID} = $order->getOrderId();

        return $cnd;
    }

    // }}}

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if (self::COLUMN_NAME == $column[static::COLUMN_CODE]) {
            $class .= ' card ' . strtolower($entity->getCardType());
        }

        return $class;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getPaymentURL(\XLite\Model\AEntity $entity)
    {
        $result = Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_xpayments_url
            . 'admin.php';

        if (
            $entity->getTransaction()
            && $entity->getTransaction()->getDataCell('xpc_txnid')
        ) {
            $result .= '?target=payment&txnid=' . $entity->getTransaction()->getDataCell('xpc_txnid')->getValue();
        }

        return $result;
    }

    /**
     * Get transaction Id 
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getTransactionId(\XLite\Model\AEntity $entity)
    {
        return $entity->getTransaction()->getTransactionId();
    }

    /**
     * Get transaction
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return \XLite\Model\Payment\Transaction or null 
     */
    protected function getTransaction(\XLite\Model\AEntity $entity)
    {
        return $entity->getTransaction();
    }

    /**
     * Payment transaction units
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return array
     */
    protected function getTransactionUnits(\XLite\Model\AEntity $entity)
    {
        $transaction = $entity->getTransaction();

        $result = false;

        if ($transaction) {
            $view = new PaymentActions();

            $result = $view->getUnitsForTransaction($transaction);
        }

        return $result;
    }

    /**
     * Get card number. Adds saved flag for saved ones
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return \XLite\Model\Payment\Transaction or null
     */
    protected function getCardNumber(\XLite\Model\AEntity $entity)
    {
        $result = $entity->getCardNumber();

        if (
            $entity->getTransaction()
            && $entity->getTransaction()->getPaymentMethod()
            && $entity->getTransaction()->getPaymentMethod()->getClass() === SavedCard::class
        ) {
            $result .= ' (Saved)';
        }

        return $result;
    }

    /**
     * Get text for the warning for potentially fraudulent transaction
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string 
     */
    public function getFraudStatusText(\XLite\Model\AEntity $entity)
    {
        $transaction = $entity->getTransaction();

        $text = false;

        $allIsPending = true;

        if ($transaction->getFraudCheckData()) {

            $text = array();

            foreach ($transaction->getFraudCheckData() as $fraudCheckData) {

                if (!$fraudCheckData->isPending()) {
                    $allIsPending = false;
                }

                if (!$fraudCheckData->isManualReview()) {
                    continue;
                }

                if ($fraudCheckData->getMessage()) {
                    $text[] = $fraudCheckData->getMessage();
                } else {
                    $text[] = $fraudCheckData->getService() . ' ' . static::t('considers this payment transaction as potentially fraudulent.');
                }
            }

            $text = implode("\n", $text);
        }

        if (empty($text)) {            
            $text = ($allIsPending)
                    ? static::t('Transaction is being reviewed for fraud.')
                    : static::t('X-Payments considers this transaction as potentially fraudulent.');
        }

        return $text;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        if (
            Manager::getRegistry()->isModuleEnabled('XC\MultiVendor')
            && Auth::getInstance()->isVendor()
        ) {
            $result = false;
        } else {
            $result = parent::isVisible();
        }

        return $result;
    }
}
