<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use XLite\Model\Payment\TransactionData;

class PaymentMethod
{
    /**
     * @param \XLite\Model\Payment\Method $method
     *
     * @param \XLite\Model\Cart|null      $cart
     *
     * @return array
     */
    public function mapMethod(\XLite\Model\Payment\Method $method, \XLite\Model\Cart $cart = null)
    {
        $title = $method->getTitle();
        $paymentName = empty($title)
            ? $method->getName()
            : $method->getTitle();

        return [
            'id'    => $method->getMethodId(),
            'service_name'  => $method->getServiceName(),
            'payment_name'  => htmlspecialchars_decode($paymentName),
            'details'       => $method->getDescription(),
            'fields'        => $this->mapFields($method, $cart),
        ];
    }

    protected function mapFields(\XLite\Model\Payment\Method $method, \XLite\Model\Cart $cart = null)
    {
        $processor = $method->getProcessor();
        if (!$processor) {
            return [];
        }

        $fields = $processor->getInputDataFields();
        $fieldsValues = [];
        if ($cart && $cart->getFirstOpenPaymentTransaction()) {
            TransactionData::$isApiMode = true;
            $fieldsDataRaw = $processor->getTransactionData(
                $cart->getFirstOpenPaymentTransaction()
            );
            TransactionData::$isApiMode = false;

            foreach ($fieldsDataRaw as $fieldRaw) {
                $fieldsValues[$fieldRaw['name']] = $fieldRaw['value'];
            }
        }

        if (!$fields) {
            return [];
        }

        return array_map(function($field, $key) use($fieldsValues) {
            return [
                'id'    => $key,
                'name'  => $field['label'],
                'value' => isset($fieldsValues[$key]) ? $fieldsValues[$key] : '',
            ];
        }, $fields, array_keys($fields));
    }
}
