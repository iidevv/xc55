<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace Qualiteam\SkinActXPaymentsConnector\Controller\Admin;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Payment\Method;

/**
 * Payment settings
 *
 * @Extender\Mixin
 */
class PaymentSettings extends \XLite\Controller\Admin\PaymentSettings
{
    /**
     * Add method
     *
     * @return void
     */
    protected function doActionAdd()
    {
        $id = Request::getInstance()->id;

        /** @var Method $method */
        $method = $id
            ? Database::getRepo(Method::class)->find($id)
            : null;

        if (
            $method
            && XPayments::class == $method->getClass()
            && true === $method->getFromMarketplace()
        ) {

            $this->setReturnURL($this->buildURL(
                'xpc', 
                '', 
                [
                    'page' => Settings::PAGE_WELCOME,
                    'method_id' => $id
                ]
            ));

        } else {

            parent::doActionAdd();

            $classes = [
                XPayments::class,
                SavedCard::class,
            ];

            if (
                $method 
                && in_array($method->getClass(), $classes)
                && false === $method->getFromMarketplace()
            )
            {
                $this->setReturnURL($this->buildURL(
                    'xpc',
                    '',
                    ['section' => 'payment_methods']
                ));
            }
        }
    }
}
