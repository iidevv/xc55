<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations\Post;

use Qualiteam\SkinActKlarna\Core\Configuration\Configuration;
use Qualiteam\SkinActKlarna\Core\Endpoints\Constructor;
use Qualiteam\SkinActKlarna\Core\Endpoints\ConstructorInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params\SetBillingAddressInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params\SetLocaleInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params\SetOrderAmountInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params\SetOrderTaxAmountInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params\SetPurchaseInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params\SetOrderLinesInterface;
use Qualiteam\SkinActKlarna\Helpers\Converter;
use Qualiteam\SkinActKlarna\Helpers\Order as OrderHelper;
use Qualiteam\SkinActKlarna\Helpers\Profile as ProfileHelper;
use XLite\Model\Cart;
use XLite\Model\Profile;

class OrderConstructor implements
    SetLocaleInterface,
    ConstructorInterface,
    SetOrderLinesInterface,
    SetOrderTaxAmountInterface,
    SetBillingAddressInterface,
    SetPurchaseInterface,
    SetOrderAmountInterface
{

    /**
     * @param \Qualiteam\SkinActKlarna\Helpers\Converter                $converter
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Constructor       $constructor
     * @param \Qualiteam\SkinActKlarna\Core\Configuration\Configuration $configuration
     * @param \Qualiteam\SkinActKlarna\Helpers\Order                    $orderHelper
     * @param \Qualiteam\SkinActKlarna\Helpers\Profile                  $profileHelper
     * @param \XLite\Model\Cart                                         $cart
     * @param \XLite\Model\Profile                                      $profile
     */
    public function __construct(
        private Converter     $converter,
        private Constructor   $constructor,
        private Configuration $configuration,
        private OrderHelper   $orderHelper,
        private ProfileHelper $profileHelper,
        private Cart          $cart,
        private Profile       $profile,
    ) {
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $this->constructor->build($this);
    }

    /**
     * Set locale
     *
     * @return void
     * @throws \Exception
     */
    public function setLocale(): void
    {
        $this->constructor->addParam(static::PARAM_LOCALE, sprintf('%s-%s',
                $this->converter->getLanguageCode($this->profile),
                $this->profileHelper->getCountryCode(),
            )
        );
    }

    /**
     * Set order lines
     *
     * @return void
     */
    public function setOrderLines(): void
    {
        $this->constructor->addParam(
            static::PARAM_ORDER_LINES,
            $this->orderHelper->getOrderLines()
        );
    }

    /**
     * Set order amount
     *
     * @return void
     */
    public function setOrderAmount(): void
    {
        $this->constructor->addParam(
            static::PARAM_ORDER_AMOUNT,
            $this->converter->getOrderAmount($this->cart)
        );
    }

    /**
     * Set order tax amount
     *
     * @return void
     */
    public function setOrderTaxAmount(): void
    {
        $this->constructor->addParam(
            static::PARAM_ORDER_TAX_AMOUNT,
            $this->converter->getOrderTaxAmount($this->cart)
        );
    }

    /**
     * Set purchase country
     *
     * @return void
     * @throws \Exception
     */
    public function setPurchaseCountry(): void
    {
        $this->constructor->addParam(
            static::PARAM_PURCHASE_COUNTRY,
            $this->profileHelper->getCountryCode()
        );
    }

    /**
     * Set purchase currency
     *
     * @return void
     */
    public function setPurchaseCurrency(): void
    {
        $this->constructor->addParam(
            static::PARAM_PURCHASE_CURRENCY,
            $this->configuration->getCurrency()
        );
    }

    /**
     * Get a constructed body
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->constructor->getBody();
    }

    /**
     * Set billing address
     *
     * @return void
     */
    public function setBillingAddress(): void
    {
        $this->constructor->addParam(
            static::PARAM_BILLING_ADDRESS,
            $this->profileHelper->getBillingAddress()
        );
    }
}