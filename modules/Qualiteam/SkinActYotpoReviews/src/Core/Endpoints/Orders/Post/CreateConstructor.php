<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Post;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ConstructorInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetCustomerInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetCustomPropertiesInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetLineItemsInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetOrderDateInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetPaymentMethodInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetPaymentStatusInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetShippingAddressInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetSubtotalPriceInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params\SetTotalPriceInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetCurrencyInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetExternalIdInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Params\SetBillingAddressInterface;
use Qualiteam\SkinActYotpoReviews\Helpers\Order as OrderHelper;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XLite\Model\Order;

class CreateConstructor implements ConstructorInterface,
    SetExternalIdInterface,
    SetOrderDateInterface,
    SetPaymentMethodInterface,
    SetTotalPriceInterface,
    SetCurrencyInterface,
    SetSubtotalPriceInterface,
    SetPaymentStatusInterface,
    SetCustomerInterface,
    SetBillingAddressInterface,
    SetShippingAddressInterface,
    SetLineItemsInterface,
    SetCustomPropertiesInterface
{
    /**
     * @var \XLite\Model\Order|null
     */
    private ?Order $order;

    /**
     * @var \XCart\Domain\ModuleManagerDomain
     */
    private ModuleManagerDomain $moduleManagerDomain;

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor $constructor
     * @param \Qualiteam\SkinActYotpoReviews\Helpers\Order              $orderHelper
     */
    public function __construct(
        private Constructor $constructor,
        private OrderHelper $orderHelper,
    ) {
        $this->moduleManagerDomain = Container::getContainer()?->get(ModuleManagerDomain::class);
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return void
     */
    public function prepareOrder(?Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $this->constructor->build($this);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return ['order' => $this->constructor->getBody()];
    }

    /**
     * @return void
     */
    public function setExternalId(): void
    {
        $this->constructor->addParam(
            self::PARAM_EXTERNAL_ID,
            $this->orderHelper->getOrderNumber($this->order)
        );
    }

    /**
     * @return void
     */
    public function setOrderDate(): void
    {
        $this->constructor->addParam(
            self::PARAM_ORDER_DATE,
            $this->orderHelper->getOrderDate($this->order)
        );
    }

    /**
     * @return void
     */
    public function setPaymentMethod(): void
    {
        $this->constructor->addParam(
            self::PARAM_PAYMENT_METHOD,
            $this->orderHelper->getOrderPaymentMethod($this->order)
        );
    }

    /**
     * @return void
     */
    public function setTotalPrice(): void
    {
        $this->constructor->addParam(
            self::PARAM_TOTAL_PRICE,
            $this->orderHelper->getOrderTotalPrice($this->order)
        );
    }

    /**
     * @return void
     */
    public function setSubtotalPrice(): void
    {
        $this->constructor->addParam(
            self::PARAM_SUBTOTAL_PRICE,
            $this->orderHelper->getOrderSubtotalPrice($this->order)
        );
    }

    /**
     * @return void
     */
    public function setCurrency(): void
    {
        $this->constructor->addParam(
            self::PARAM_CURRENCY,
            \XLite::getInstance()->getCurrency()->getCode()
        );
    }

    /**
     * @return void
     */
    public function setPaymentStatus(): void
    {
        $this->constructor->addParam(
            self::PARAM_PAYMENT_STATUS,
            $this->orderHelper->getOrderPaymentStatus($this->order)
        );
    }

    /**
     * @return void
     */
    public function setCustomer(): void
    {
        $this->constructor->addParam(
            self::PARAM_CUSTOMER,
            $this->orderHelper->getOrderCustomer($this->order)
        );
    }

    /**
     * @return void
     */
    public function setBillingAddress(): void
    {
        $this->constructor->addParam(
            self::PARAM_BILLING_ADDRESS,
            $this->orderHelper->getOrderBillingAddress($this->order)
        );
    }

    /**
     * @return void
     */
    public function setShippingAddress(): void
    {
        $this->constructor->addParam(
            self::PARAM_SHIPPING_ADDRESS,
            $this->orderHelper->getOrderShippingAddress($this->order)
        );
    }

    /**
     * @return void
     */
    public function setLineItems(): void
    {
        $this->constructor->addParam(
            self::PARAM_LINE_ITEMS,
            $this->orderHelper->getOrderLineItems($this->order)
        );
    }

    /**
     * @return void
     */
    public function setCustomProperties(): void
    {
        $this->constructor->addParam(
            self::PARAM_CUSTOM_PROPERTIES,
            $this->orderHelper->getOrderCustomProperties($this->order)
        );
    }
}