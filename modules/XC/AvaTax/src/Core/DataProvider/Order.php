<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Core\DataProvider;

use XC\AvaTax\Core\TaxCore;
use XLite\Core\Config;

class Order
{
    public const REFUND_POSTFIX = 'R'; // minimize size to fit in 50 symbols limit

    private \XLite\Model\Order $order;

    public function __construct(\XLite\Model\Order $order)
    {
        $this->order = $order;
    }

    public function getTransactionCode(): string
    {
        // to work XC54 + XC55 + dev + live simultaneously
        $shopURL = \XLite::getInstance()->getShopURL();
        $shopURL = str_ireplace('http://', 'https://', $shopURL);

        return $this->order->getOrderNumber()
            ? ($this->order->getOrderNumber() . '-' . md5($shopURL)) . (Config::getInstance()->XC->AvaTax->dev_order_prefix ?? '')
            : '';
    }

    public function transactionCodeEncoded(string $postfix = ''): string
    {
        return $this->prepareForURL($this->getTransactionCode() . $postfix);
    }

    public function companyCodeEncoded(): string
    {
        return $this->prepareForURL(Config::getInstance()->XC->AvaTax->companycode);
    }

    /**
     * https://developer.avalara.com/api-reference/avatax/rest/v2/methods/Transactions/VoidTransaction/
     */
    public function getVoidTransactionModel(string $reason): array
    {
        return ['code' => $reason];
    }

    /**
     * https://developer.avalara.com/api-reference/avatax/rest/v2/methods/Transactions/AdjustTransaction/
     */
    public function getAdjustTransactionModel(array $newOrderData, string $reason, string $reasonDescription = ''): array
    {
        $newOrderData['commit'] = Config::getInstance()->XC->AvaTax->commit; // wo runtime component from \XC\AvaTax\Core\TaxCore::isCommitOnPlaceOrder
        $newOrderData['type']   = Config::getInstance()->XC->AvaTax->record_transactions ? 'SalesInvoice' : 'SalesOrder'; // wo runtime component from \XC\AvaTax\Core\TaxCore::isCommitOnPlaceOrder;

        $result = [
            'newTransaction'   => $newOrderData,
            'adjustmentReason' => $reason,
        ];

        if (empty($reasonDescription)) {
            if ($reason === TaxCore::OTHER) {
                $result['adjustmentReason'] = TaxCore::PRICE_ADJUSTED;
            }
        } else {
            $reasonDescription = str_replace('Array', '', $reasonDescription);
            $reasonDescription = preg_replace('/\s+/i', ' ', $reasonDescription);
            $reasonDescription = preg_replace('/\)$/', '', $reasonDescription);
            $reasonDescription = trim($reasonDescription, "( \n\r\t\v");
            if (strlen($reasonDescription) > 254) {
                $reasonDescription = substr($reasonDescription, 0, 251) . '...'; // To avoid error "message": "AdjustmentDescription length must be between 1 and 255 characters."
            }
            $result['adjustmentDescription'] = $reasonDescription;
        }

        return $result;
    }

    /**
     * https://developer.avalara.com/api-reference/avatax/rest/v2/methods/Transactions/RefundTransaction/
     */
    public function getRefundTransactionModel(\XLite\Model\Payment\BackendTransaction $transaction): array
    {
        $refundType = $transaction->isFullRefund() ? 'Full' : 'Percentage';

        $result = [
            'refundTransactionCode' => $this->getTransactionCode() . self::REFUND_POSTFIX . $transaction->getId(),
            'refundDate'            => date('Y-m-d', $transaction->getDate()),
            'refundType'            => $refundType,
        ];

        if (strlen($result['refundTransactionCode']) > 50) {
            // To avoid https://developer.avalara.com/avatax/errors/FieldLengthError
            unset($result['refundTransactionCode']);
        }

        if ($refundType === 'Percentage') {
            // 1. The percentage number for a Percentage refund must be between 0 and 100. https://developer.avalara.com/avatax/errors/RefundPercentageOutOfRange/
            // 2. getParentValue() isn't used to preserve max precision
            // 3. $transaction->getPaymentTransaction()->getValue() is incorrect for multi-vendor
            $result['refundPercentage'] = $transaction->getValue() * 100 / $this->order->getTotal();
        }

        return $result;
    }

    private function prepareForURL(string $str): string
    {
        $map = [
            '/' => '_-ava2f-_',
            '+' => '_-ava2b-_',
            '?' => '_-ava3f-_',
            '%' => '_-ava25-_',
            '#' => '_-ava23-_',
            ' ' => '%20',
        ];

        return strtr($str, $map);
    }
}
