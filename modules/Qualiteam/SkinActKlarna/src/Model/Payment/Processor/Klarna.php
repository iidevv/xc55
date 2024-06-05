<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Model\Payment\Processor;

use Qualiteam\SkinActKlarna\Core\Validators\ResponseValidator;
use Qualiteam\SkinActKlarna\Model\OrderKlarna;
use Qualiteam\SkinActKlarna\Model\OrderKlarnaRefund;
use XCart\Container;
use XLite\Core\Database;
use XLite\Core\Session;
use XLite\Core\TopMessage;
use XLite\Model\Order;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Method;

class Klarna extends \XLite\Model\Payment\Base\CreditCard
{
    /**
     * @var array
     */
    protected array $klarnaOrderResponse;

    /**
     * @var string
     */
    protected string $procedureResult;

    /**
     * @var array
     */
    protected array $klarnaRefundResponse;

    /**
     * @var string
     */
    protected string $backendTransactionStatus;

    /**
     * @var \XLite\Model\Order
     */
    protected Order $backendTransactionOrder;

    public function getAllowedTransactions()
    {
        return [
            BackendTransaction::TRAN_TYPE_SALE,
            BackendTransaction::TRAN_TYPE_REFUND,
            BackendTransaction::TRAN_TYPE_REFUND_PART,
            BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        ];
    }

    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes(): array
    {
        return [
            self::OPERATION_SALE,
        ];
    }

    public function getSettingsWidget()
    {
        return 'modules/Qualiteam/SkinActKlarna/config.twig';
    }

    public function getAdminIconURL(Method $method)
    {
        return true;
    }

    public function getCheckoutTemplate(Method $method)
    {
        return 'modules/Qualiteam/SkinActKlarna/checkout/klarna.twig';
    }

    protected function getFormFields()
    {
        $container = Container::getContainer()->get('klarna.service.api.payments.authorizations.order');

        return $container->getBody();
    }

    /**
     * @throws \Exception
     */
    protected function doInitialPayment()
    {
        $this->prepareKlarnaCreateOrderUrl();
        $this->getOrderKlarnaResponse();

        if (ResponseValidator::isValid($this->klarnaOrderResponse)) {
            $orderKlarna = $this->createOrderKlarna();

            $this->updateOrder($orderKlarna);
            $this->saveKlarnaObject($orderKlarna);

            $this->clearKlarnaSessions();
            $this->updatePaymentProcedureResult(static::COMPLETED);
        } else {
            $this->showErrorMessage();
            $this->updatePaymentProcedureResult(static::FAILED);
        }

        return $this->procedureResult;
    }

    /**
     * @return void
     */
    protected function prepareKlarnaCreateOrderUrl(): void
    {
        $url = Container::getContainer()->get('klarna.service.api.payments.authorization.create.dynamic.url');
        $url->setParam($this->getOrder()->getAuth());
    }

    /**
     * @return void
     */
    protected function getOrderKlarnaResponse(): void
    {
        $container                 = Container::getContainer()->get('klarna.service.api.payments.authorizations.order');
        $this->klarnaOrderResponse = $container->getData();
    }

    /**
     * @return \Qualiteam\SkinActKlarna\Model\OrderKlarna
     * @throws \Exception
     */
    protected function createOrderKlarna(): OrderKlarna
    {
        $orderKlarna = new OrderKlarna;
        $orderKlarna->setOrder($this->transaction->getOrder());
        $orderKlarna->setKlarnaOrderId($this->klarnaOrderResponse['order_id']);
        $orderKlarna->setFraudStatus($this->klarnaOrderResponse['fraud_status']);
        $orderKlarna->setAuthorizedPaymentMethod($this->klarnaOrderResponse['authorized_payment_method']['type']);

        return $orderKlarna;
    }

    /**
     * @param \Qualiteam\SkinActKlarna\Model\OrderKlarna $orderKlarna
     *
     * @return void
     */
    protected function updateOrder(OrderKlarna $orderKlarna): void
    {
        $this->getOrder()->setOrderKlarna($orderKlarna);
    }

    /**
     * @param \Qualiteam\SkinActKlarna\Model\OrderKlarna|\Qualiteam\SkinActKlarna\Model\OrderKlarnaRefund $object
     *
     * @return void
     * @throws \Exception
     */
    protected function saveKlarnaObject(OrderKlarna|OrderKlarnaRefund $object): void
    {
        Database::getEM()->persist($object);
        Database::getEM()->flush();
    }

    /**
     * @param string $result
     *
     * @return void
     */
    protected function updatePaymentProcedureResult(string $result): void
    {
        $this->procedureResult = $result;
    }

    /**
     * @return void
     */
    protected function clearKlarnaSessions(): void
    {
        unset(Session::getInstance()->klarna_session);
        unset(Session::getInstance()->klarna_profile);
    }

    /**
     * @return void
     */
    protected function showErrorMessage(): void
    {
        $messages = json_decode($this->klarnaOrderResponse['message'], true);

        foreach ($messages['error_messages'] as $message) {
            $this->transaction->setNote($message);
        }
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     * @throws \Exception
     */
    protected function doRefundPart(BackendTransaction $transaction): bool
    {
        return $this->doRefund($transaction);
    }

    /**
     * @throws \Exception
     */
    protected function doRefund(BackendTransaction $transaction): bool
    {
        $this->backendTransactionOrder  = $transaction->getPaymentTransaction()->getOrder();
        $this->backendTransactionStatus = BackendTransaction::STATUS_FAILED;

        $this->prepareTransactionParam($transaction);
        $this->prepareRefundDynamicUrl();
        $this->getKlarnaRefundResponse();

        if (ResponseValidator::isValid($this->klarnaRefundResponse)) {
            $refund = $this->createOrderKlarnaRefund($transaction);

            $this->updateOrderKlarna($refund);
            $this->saveKlarnaObject($refund);

            $this->updateBackendTransactionStatus(BackendTransaction::STATUS_SUCCESS, $transaction);

            $this->showSuccessRefundMessage();
        } else {
            $this->showRefundErrorTopMessage();
        }

        return $this->backendTransactionStatus == BackendTransaction::STATUS_SUCCESS;
    }

    /**
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     *
     * @return void
     */
    protected function prepareTransactionParam(BackendTransaction $transaction): void
    {
        Container::getContainer()->get('Qualiteam\SkinActKlarna\Core\Endpoints\Params')->setTransaction(
            $transaction
        );
    }

    /**
     * @return void
     */
    protected function prepareRefundDynamicUrl(): void
    {
        $klarnaOrderId = $this->backendTransactionOrder->getOrderKlarna()->getKlarnaOrderId();
        $container     = Container::getContainer()->get('klarna.service.api.ordermanagement.orders.refund.dynamic.url');
        $container->setParam($klarnaOrderId);
    }

    /**
     * @return void
     */
    protected function getKlarnaRefundResponse(): void
    {
        $this->klarnaRefundResponse = $this->getKlarnaRefundData();
    }

    /**
     * @return array
     */
    protected function getKlarnaRefundData(): array
    {
        $container = Container::getContainer()->get('klarna.service.api.ordermanagement.orders.refund');

        return $container->getData();
    }

    /**
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     *
     * @return \Qualiteam\SkinActKlarna\Model\OrderKlarnaRefund
     */
    protected function createOrderKlarnaRefund(BackendTransaction $transaction): OrderKlarnaRefund
    {
        $refund = new OrderKlarnaRefund;
        $refund->setDate(time());
        $refund->setUrl($this->getKlarnaRefundHeaderItem('Location'));
        $refund->setAmount($transaction->getValue());
        $refund->setRefundId($this->getKlarnaRefundHeaderItem('Refund-Id'));
        $refund->setOrderKlarna($this->backendTransactionOrder->getOrderKlarna());

        return $refund;
    }

    /**
     * @param string $item
     *
     * @return string
     */
    protected function getKlarnaRefundHeaderItem(string $item): string
    {
        return $this->klarnaRefundResponse['headers'][$item][0];
    }

    /**
     * @param \Qualiteam\SkinActKlarna\Model\OrderKlarnaRefund $orderKlarnaRefund
     *
     * @return void
     */
    protected function updateOrderKlarna(OrderKlarnaRefund $orderKlarnaRefund): void
    {
        $this->backendTransactionOrder->getOrderKlarna()->setRefund($orderKlarnaRefund);
    }

    /**
     * @param string                                  $status
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     *
     * @return void
     */
    protected function updateBackendTransactionStatus(string $status, BackendTransaction $transaction): void
    {
        $this->backendTransactionStatus = $status;
        $transaction->setStatus($status);
    }

    /**
     * @return void
     */
    protected function showSuccessRefundMessage(): void
    {
        TopMessage::addInfo('SkinActKlarna refund successful');
    }

    /**
     * @return void
     */
    protected function showRefundErrorTopMessage(): void
    {
        $message = json_decode($this->klarnaRefundResponse['message'], true);
        foreach ($message['error_messages'] as $errorMessage) {
            TopMessage::addError($errorMessage);
        }
    }

    /**
     * Refund
     *
     * @param BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     * @throws \Exception
     */
    protected function doRefundMulti(BackendTransaction $transaction): bool
    {
        return $this->doRefund($transaction);
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method): bool
    {
        return parent::isConfigured($method)
            && $method->getSetting('username')
            && $method->getSetting('password');
    }
}