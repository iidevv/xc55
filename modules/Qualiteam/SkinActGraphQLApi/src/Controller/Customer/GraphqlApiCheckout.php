<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Customer;

use Qualiteam\SkinActGraphQLApi\Controller\Features\GraphqlApiContextTrait;
use XLite\Core\Config;
use Qualiteam\SkinActGraphQLApi\View\ApiCheckout;

class GraphqlApiCheckout extends \XLite\Controller\Customer\Checkout
{
    use GraphqlApiContextTrait;

    protected function doNoAction()
    {
        if (!$this->getCart() || !$this->getCart()->isApiCart()) {
            exit;
        }

        if (!$this->isCheckoutNeeded()) {
            $this->redirect(
                \XLite\Core\Converter::buildFullURL(
                    'checkout',
                    '',
                    [
                        '_token'  => $this->getCartToken(),
                        'payment' => $this->buildPaymentQueryParam(),
                        'shopKey' => Config::getInstance()->Internal->shop_key,
                    ],
                    \XLite::getCustomerScript()
                )
            );
        }

        parent::doNoAction();
    }

    protected function buildPaymentQueryParam()
    {
        if (
            !$this->getCart()
            || !$this->getCart()->getPaymentProcessor()
            || !$this->getCart()->getFirstOpenPaymentTransaction()
        ) {
            return '';
        }

        $fieldsRaw = $this->getCart()->getPaymentProcessor()->getTransactionData(
            $this->getCart()->getFirstOpenPaymentTransaction(),
            false
        );

        $fields = [];

        foreach ($fieldsRaw as $fieldRaw) {
            $fields[$fieldRaw['name']] = $fieldRaw['value'];
        }

        return $fields;
    }

    /**
     * @return string
     */
    protected function getViewerClass()
    {
        return ApiCheckout::class;
    }

    /**
     * Get list of payment methods that require hidden checkout
     *
     * @return array
     */
    protected function getCheckoutPaymentList()
    {
        return [
            'Stripe',
        ];
    }

    /**
     * Check if cart needs
     *
     * @return boolean
     */
    protected function isCheckoutNeeded()
    {
        return $this->getCart()
            && in_array(
                $this->getCart()->getPaymentMethod()->getServiceName(),
                $this->getCheckoutPaymentList(),
                true
            );
    }
}
