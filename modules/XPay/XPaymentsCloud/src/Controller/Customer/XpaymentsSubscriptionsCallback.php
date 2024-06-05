<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Controller\Customer;

use XLite\Core\Database;
use XLite\Core\Exception\PaymentProcessing\CallbackRequestError;
use XLite\Core\Request;
use XLite\Model\Payment\Base\Online;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;
use XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XPaymentsCloud\ApiException;
use XPaymentsCloud\Model\Subscription as XPaymentsSubscription;

class XpaymentsSubscriptionsCallback extends \XLite\Controller\Customer\ACustomer
{
    const STATUS_SUBSCRIPTION_NOT_FOUND = 'subscriptionNotFound';
    const STATUS_SUCCESS                = 'success';
    const STATUS_FAILED                 = 'failed';

    /**
     * @return void
     * @throws ApiException
     * @throws CallbackRequestError
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handleRequest()
    {
        if (Request::getInstance()->isPost()) {

            try {
                $api = XPaymentsHelper::getClient();
                $response = $api->parseCallback();
            } catch (ApiException $exception) {
                throw new CallbackRequestError($exception->getMessage());
            }

            $action = $response->action ?? 'createOrder';
            /** @var XPaymentsSubscription $xpaymentsSubscription */
            $xpaymentsSubscription = $response->getSubscription();
            $xpaymentsPayment = $response->getPayment();
            $subscriptionId = '';
            if ($xpaymentsSubscription) {
                $subscriptionId = $xpaymentsSubscription->getPublicId();
            }

            if ($subscriptionId) {

                /** @var Subscription $subscription */
                $subscription = Database::getRepo('XPay\XPaymentsCloud\Model\Subscription\Subscription')
                    ->findOneBy(['xpaymentsSubscriptionPublicId' => $subscriptionId]);

                if ($subscription) {

                    switch ($action) {

                        case 'createOrder':
                            $order = $subscription->createOrder();
                            $order->processSucceed();
                            if ($order) {
                                /** @var \XLite\Model\Payment\Transaction $transaction */
                                $transaction = $order->getPaymentTransactions()->last();
                                if ($transaction) {
                                    $transaction->setXpaymentsId($xpaymentsPayment->xpid);
                                    $refId = $transaction->getPublicTxnId();
                                    $recurringAmount = $order->getTotal();
                                    $updateParams = [
                                        'recurringAmount'    => $recurringAmount,
                                        'refId'              => $refId,
                                        'paymentCallbackUrl' => $this->getPaymentCallbackUrl($refId),
                                    ];
                                    $updatedResponse = $api->doUpdateSubscription($subscriptionId, $updateParams);
                                    if ($updatedResponse->getSubscription()) {
                                        $subscription->setDataFromApi($updatedResponse->getSubscription());
                                    }
                                }
                            }
                            break;

                        case 'updateSubscription':
                            $subscription->setDataFromApi($xpaymentsSubscription);
                            break;
                    }

                    // Flush database
                    $subscription->update();
                }
            }
        }
    }

    /**
     * Return callback URL for upcoming payment
     *
     * @param string $refId
     *
     * @return string
     */
    protected function getPaymentCallbackUrl(string $refId)
    {
        $query = [
            'txn_id_name' => Online::RETURN_TXN_ID,
        ];

        $query[$query['txn_id_name']] = $refId;

        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('callback', '', $query, \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

}
