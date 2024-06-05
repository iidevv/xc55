<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use Qualiteam\SkinActGraphQLApi\Core\UrlHelper;
use XcartGraphqlApi\DTO\CartDTO;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart\PaymentMethods;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart\ShippingMethods;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;
use XLite\Core\Config;
use XLite\Core\Converter;

/**
 * Class Cart
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper
 */
class Cart
{
    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Cart constructor.
     *
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return CartDTO
     */
    public function mapToDto(\XLite\Model\Cart $cart)
    {
        /** @var \Qualiteam\SkinActGraphQLApi\Model\Order $cart */
        $dto = new CartDTO();

        $dto->cartModel = $cart;

        $dto->id = $cart->getOrderId();
        $dto->token = $cart->getGraphQLAuthToken();

        $dto->cart_url = UrlHelper::insertWebAuth($this->mapCartUrl($cart, $cart->getApiCartUniqueId()));
        $dto->checkout_url = UrlHelper::insertWebAuth($this->mapCheckoutUrl($cart, $cart->getApiCartUniqueId()));
        $dto->webview_flow_url = UrlHelper::insertWebAuth($this->mapWebViewFlowUrl($cart, $cart->getApiCartUniqueId()));

        $dto->total = $this->roundPriceForCart($cart, $cart->getTotal());
        $dto->total_amount = $cart->countQuantity();

        // To be resolved by underlying resolvers
        $dto->address_list = $this->mapAddressList($cart);
        $dto->payment = $this->mapPayment($cart);
        $dto->shipping = $this->mapShipping($cart);
        $dto->user = $cart->getOrigProfile();
        $dto->items = $cart->getItems();

        $dto->same_address = $cart->getProfile()->isSameAddress();
        $dto->notes = $cart->getNotes();

        $dto->payment_methods = new PaymentMethods(
            new Mapper\PaymentMethod(),
            $this->cartService
        );

        $dto->shipping_methods = new ShippingMethods(
            new Mapper\ShippingMethodRate(),
            $this->cartService
        );

        $dto->errors = $this->errors;
        $dto->checkout_ready = $this->isCheckoutReady($cart);
        $dto->payment_selection_ready = $this->isPaymentSelectionReady($cart);

        return $dto;
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return bool
     */
    protected function isCheckoutReady($cart)
    {
        return empty($this->errors) && $cart->checkCart();
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return bool
     */
    protected function isPaymentSelectionReady($cart)
    {
        $relevantErrors = array_filter($this->errors, static function ($error) {
            return $error !== CartDTO::NO_PAYMENT_SELECTED;
        });

        return empty($relevantErrors);
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    protected function mapAddressList(\XLite\Model\Cart $cart)
    {
        $addressList = [];

        $profile = $cart->getProfile();

        if ($profile) {
            $shippingAddress = $profile->getShippingAddress();
            $billingAddress = $profile->getBillingAddress();

            if ($shippingAddress) {
                $addressList['S'] = $shippingAddress;
            }

            if ($billingAddress) {
                $addressList['B'] = $billingAddress;
            }
        }

        return $addressList;
    }

    protected function mapCartUrl($cart, $token)
    {
        return Converter::buildFullURL(
            'graphql_api_cart',
            '',
            [
                '_token' => $token,
                'mode' => 'checkout',
                'shopKey' => Config::getInstance()->Internal->shop_key,
            ],
            \XLite::getCustomerScript()
        );
    }

    protected function mapCheckoutUrl($cart, $token)
    {
        return Converter::buildFullURL(
            'graphql_api_checkout',
            '',
            [
                '_token' => $token,
                'shopKey' => Config::getInstance()->Internal->shop_key,
            ],
            \XLite::getCustomerScript()
        );
    }

    protected function mapWebViewFlowUrl($cart, $token)
    {
        return Converter::buildFullURL(
            'graphql_api_cart',
            '',
            [
                '_token' => $token,
                'mode' => 'cart',
                'shopKey' => Config::getInstance()->Internal->shop_key,
            ],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return \XLite\Model\Payment\Method|null
     */
    protected function mapPayment(\XLite\Model\Cart $cart)
    {
        $paymentMethod = $cart->getPaymentMethod();

        $payment = null;
        if ($paymentMethod) {
            $payment = $paymentMethod;
        } else {
            $this->errors[] = CartDTO::NO_PAYMENT_SELECTED;
        }

        return $payment;
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return \XLite\Model\Shipping\Rate|null
     */
    protected function mapShipping(\XLite\Model\Cart $cart)
    {
        /** @var \XLite\Logic\Order\Modifier\Shipping $shippingModifier */
        $shippingModifier = $cart->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        $shipping = null;
        if ($shippingModifier) {
            $method = $shippingModifier->getMethod();

            if ($method) {
                $shipping = $shippingModifier->getSelectedRate();
            }
        }

        return $shipping;
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param                   $value
     *
     * @return float
     */
    protected function roundPriceForCart(\XLite\Model\Cart $cart, $value)
    {
        return $cart->getCurrency()
            ? $cart->getCurrency()->roundValue($value)
            : $value;
    }
}
