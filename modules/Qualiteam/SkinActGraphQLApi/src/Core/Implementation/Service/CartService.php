<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Service;

use Doctrine\ORM\ORMException;
use Includes\Utils\Module\Manager;
use XcartGraphqlApi\Types\Enum\AddressTypeEnumType;
use XLite\Core\Database;
use XLite\Core\Session;
use XLite\Model\Address;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Operation\UpdateAddress;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CartServiceException;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\InvalidAmountException;
use Qualiteam\SkinActGraphQLApi\Model\Cart;
use Qualiteam\SkinActGraphQLApi\Model\Order;
use Qualiteam\SkinActGraphQLApi\Model\Profile;

class CartService
{
    /**
     * @var \XLite\Model\Cart
     */
    protected $_cart;

    /**
     * @var XCartContext
     */
    protected $_runtimeContext;

    /**
     * @param XCartContext $context
     *
     * @return \XLite\Model\Cart
     * @throws \Exception
     */
    public function retrieveCart($context)
    {
        try {
            if (!$this->_cart) {
                $this->setRuntimeContext($context);
                $this->_cart = $this->retrieveCartForContext($context);

                // Generate access token for anonymous user
                if ($this->getRuntimeContext() && !$this->getRuntimeContext()->isAuthenticated()) {
                    $this->_cart->setGraphQLAuthToken($this->getRuntimeContext()->getAuthService()->generateToken($this->_cart->getOrigProfile()));
                }
            }

            return $this->_cart;
        } catch (ORMException $e) {
            throw new \RuntimeException("Internal error occured while trying to find cart");
        }
    }

    /**
     * @return XCartContext
     */
    protected function getRuntimeContext()
    {
        return $this->_runtimeContext;
    }

    /**
     * @param XCartContext $context
     */
    protected function setRuntimeContext(XCartContext $context)
    {
        $this->_runtimeContext = $context;
    }

    /**
     * @return null $string
     */
    public function getCartApiToken()
    {
        return $this->_cart ? $this->_cart->getApiCartUniqueId() : null;
    }

    /**
     * @param \XLite\Model\Cart    $cart
     * @param \XLite\Model\Product $product
     * @param int                  $amount
     * @param array                $attributes
     *
     * @throws \Exception
     */
    public function addItem(\XLite\Model\Cart $cart, \XLite\Model\Product $product, $amount, $attributes)
    {
        $item = new \XLite\Model\OrderItem();
        $item->setOrder($cart);
        $item->setProduct($product);

        $item = $this->processOrderItem($cart, $item, $amount, $attributes);

        $cart->addItem($item);

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param string            $itemId
     * @param string            $amountModifier
     *
     * @throws CartServiceException
     */
    public function changeItemAmountByModifier(\XLite\Model\Cart $cart, $itemId, $amountModifier)
    {
        $item = $cart->getItemByItemId($itemId);

        if (!$item) {
            throw new CartServiceException("Cart item $itemId not found");
        }

        $amount = (int) $amountModifier;

        if (
            str_starts_with($amountModifier, '+')
            || (!str_contains($amountModifier, '+') && $amount > 0)
            || str_starts_with($amountModifier, '-')
        ) {
            $amount = $item->getAmount() + $amount;
        }

        $this->changeItemAmount($cart, $itemId, $amount);
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param string            $itemId
     * @param int               $amount
     *
     * @throws CartServiceException
     * @throws ORMException
     */
    public function changeItemAmount(\XLite\Model\Cart $cart, $itemId, $amount)
    {
        $item = $cart->getItemByItemId($itemId);

        if (!$item) {
            throw new CartServiceException("Cart item $itemId not found");
        }

        // Correct amount
        if (
            $item->getProductAvailableAmount() < $amount
            && $item->getProduct()->getInventoryEnabled()
        ) {
            $amount = $item->getProductAvailableAmount();
        }

        if ($amount === 0) {
            $this->deleteCartItem($cart, $itemId);
        } else {
            $item->setAmount($amount);
        }

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param string            $itemId
     *
     * @throws CartServiceException
     * @throws ORMException
     */
    public function deleteCartItem(\XLite\Model\Cart $cart, $itemId)
    {
        $item = $cart->getItemByItemId($itemId);

        if (!$item) {
            throw new CartServiceException("Cart item $itemId not found");
        }

        $cart->getItems()->removeElement($item);
        Database::getEM()->remove($item);
        Database::getEM()->flush();

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @throws CartServiceException
     */
    public function deleteCart(\XLite\Model\Cart $cart)
    {
        throw new CartServiceException('Not implemented yet');
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @throws CartServiceException
     */
    public function clearCart(\XLite\Model\Cart $cart)
    {
        $cart->clear();
        $this->updateCart($cart);
    }

    /**
     * @param string $type
     *
     * @return bool
     * @throws \Exception
     */
    public function validateAddressType($type)
    {
        $types = [AddressTypeEnumType::SHIPPING_TYPE,
                  AddressTypeEnumType::BILLING_TYPE];

        if (!in_array($type, $types, true)) {
            return false;
        }

        return true;
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param integer           $addressId
     * @param string            $type
     *
     * @throws \Exception
     */
    public function selectCartAddress(\XLite\Model\Cart $cart, $addressId, $type)
    {
        $profile = $cart->getProfile();

        /** @var Address $address */
        $address = Database::getRepo(Address::class)->find($addressId);

        if (!$address) {
            throw new \Exception("Address not found");
        }

        if (
            $type === AddressTypeEnumType::BILLING_TYPE
            && $address !== $profile->getShippingAddress()
            && $profile->getShippingAddress()
            && $cart->getProfile()->isEqualAddress()
        ) {
            $profile->getShippingAddress()->setIsBilling(false);
            Session::getInstance()->same_address = null;
        }

        if ($type === AddressTypeEnumType::SHIPPING_TYPE) {
            $profile->setShippingAddress($address);
        } else {
            $profile->setBillingAddress($address);
        }

        $this->setAsBillingIfNotPresent($profile, $address);

        Session::getInstance()->same_address = $cart->getProfile()->isEqualAddress();

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param array             $addressData
     * @param string            $type
     *
     * @param bool              $createNew
     *
     * @throws ORMException
     * @throws \Exception
     */
    public function changeCartAddress(\XLite\Model\Cart $cart, $addressData, $type, $createNew = false)
    {
        $profile      = $cart->getProfile();
        $address = null;

        if (isset($addressData['email']) && $addressData['email']) {
            if ($profile->getLogin()) {
                // NOTE: should not set login here, its only cart email
                $profile->setLastCheckoutEmail($addressData['email']);
            } else {
                // Well, in case of anonymous you apparently should
                $profile->setLogin($addressData['email']);
            }
        }

        if (
            $type === AddressTypeEnumType::BILLING_TYPE
            && $cart->getProfile()->isEqualAddress()
        ) {
            $createNew = true;
            Session::getInstance()->same_address = null;
        }

        if (!$createNew) {
            if ($type === AddressTypeEnumType::SHIPPING_TYPE) {
                $address = $profile->getShippingAddress();
            } else {
                $address = $profile->getBillingAddress();
            }
        }

        if ($address === null) {
            $address = new Address();

            $address->setProfile($profile);
            Database::getEM()->persist($address);
        }

        $address = (new UpdateAddress())($address, $addressData);

        if ($type === AddressTypeEnumType::SHIPPING_TYPE) {
            $profile->setShippingAddress($address);
        } else {
            $profile->setBillingAddress($address);
        }

        $this->setAsBillingIfNotPresent($profile, $address);

        Session::getInstance()->same_address = $cart->getProfile()->isEqualAddress();

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Profile $profile
     * @param Address              $addressToSet
     */
    protected function setAsBillingIfNotPresent($profile, $addressToSet)
    {
        if (
            $addressToSet
            && !$profile->getBillingAddress()
            && (
                !isset(Session::getInstance()->same_address)
                || Session::getInstance()->same_address === null
            )
        ) {
            // Same address as default behavior
            $addressToSet->setIsBilling(true);
        }
    }

    /**
     * @param \XLite\Model\Cart            $cart
     * @param \XLite\Model\Shipping\Method $method
     *
     * @throws \Exception
     */
    public function changeShippingMethod(\XLite\Model\Cart $cart, \XLite\Model\Shipping\Method $method)
    {
        $shippingId = $method->getMethodId();

        $cart->setLastShippingId($shippingId);
        $cart->setShippingId($shippingId);

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Cart           $cart
     * @param \XLite\Model\Payment\Method $method
     *
     * @throws \Exception
     */
    public function changePaymentMethod(\XLite\Model\Cart $cart, \XLite\Model\Payment\Method $method)
    {
        $cart->setPaymentMethod($method);
        $cart->getProfile()->setLastPaymentId($method->getMethodId());

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param string            $notes
     *
     * @throws \Exception
     */
    public function changeCustomerNotes(\XLite\Model\Cart $cart, $notes)
    {
        $cart->setNotes($notes);

        $this->updateCart($cart);
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param array             $fields
     *
     * @throws CartServiceException
     */
    public function changePaymentFields(\XLite\Model\Cart $cart, $fields)
    {
        $transaction = $cart->getFirstOpenPaymentTransaction();

        if (!$transaction) {
            throw new CartServiceException('Can\'t find transaction for cart');
        }
        $processor = $cart->getPaymentProcessor();
        if (!$processor) {
            throw new CartServiceException('Can\'t find payment processor for cart');
        }

        $fieldsFromProcessor = $processor->getInputDataFields();

        foreach ($fields as $field) {
            $name      = $field['id'];
            $fieldData = $fieldsFromProcessor[$name] ?? null;
            if (!$fieldData) {
                continue;
            }

            $transaction->setDataCell(
                $name,
                $field['value'],
                $fieldData['label'],
                $fieldData['accessLevel']
            );
        }

        $this->updateCart($cart);
    }

    /**
     * Get cart payment methods
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    public function getCartPaymentMethods(\XLite\Model\Cart $cart)
    {
        /**
         * @var \XLite\Model\Payment\Method[] $payments
         */
        return $cart->getPaymentMethods();
    }

    /**
     * @param \XLite\Model\Cart $cart
     * @param                   $value
     *
     * @return float
     */
    public function roundPriceForCart(\XLite\Model\Cart $cart, $value)
    {
        return $cart->getCurrency()
            ? $cart->getCurrency()->roundValue($value)
            : $value;
    }

    /**
     * Get cart shipping methods
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    public function getCartShippingRates(\XLite\Model\Cart $cart)
    {
        /** @var \XLite\Logic\Order\Modifier\Shipping $modifier */
        $modifier = $cart->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        /** @var \XLite\Model\Shipping\Rate[] $shippingRates */
        return $modifier->getRates();
    }

    /**
     * Process order item before adding it to cart
     *
     * @param \XLite\Model\Cart      $cart
     * @param \XLite\Model\OrderItem $item
     * @param int                    $amount
     * @param array                  $attributes
     *
     * @return \XLite\Model\OrderItem
     *
     * @throws \Exception
     */
    protected function processOrderItem(
        \XLite\Model\Cart $cart,
        \XLite\Model\OrderItem $item,
        $amount,
        $attributes
    ) {
        $product = $item->getObject();

        $item->setAttributeValues($product->prepareAttributeValues($attributes));

        $availableAmount = $this->getAvailableAmount($cart, $item);

        if (
            $availableAmount < $amount
            && $item->getProduct()->getInventoryEnabled()
        ) {
            if ($availableAmount > 0) {
                $error = (string) \XLite\Core\Translation::lbl(
                    'You tried to buy more items of "{{product}}" product {{description}} than are in stock. We have {{amount}} item(s) only. Please adjust the product quantity.',
                    [
                        'product'     => $item->getProduct()->getName(),
                        'description' => $item->getExtendedDescription(),
                        'amount'      => $availableAmount,
                    ]
                );
            } else {
                $error = (string) \XLite\Core\Translation::lbl('This item is out of stock');
            }

            throw new InvalidAmountException($error, $availableAmount);
        }

        if (
            Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants')
            && $item->getProduct()->mustHaveVariants()
        ) {
            $item->setVariant(
                $item->getProduct()->getVariantByAttributeValuesIds(
                    $item->getAttributeValuesIds()
                )
            );
        }

        $item->setAmount($amount);

        return $item;
    }

    /**
     * Get available amount for order item
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return integer
     */
    protected function getAvailableAmount(\XLite\Model\Cart $cart, \XLite\Model\OrderItem $item)
    {
        $availableAmount = $item->getProductAvailableAmount();

        /** @var \XLite\Model\OrderItem $existingItem */
        foreach ($cart->getItems() as $existingItem) {
            if ($existingItem->getProduct()->getProductId() === $item->getProduct()->getProductId()) {
                $amountLeft      = $availableAmount - $existingItem->getAmount();
                $availableAmount = max($amountLeft, 0);
            }
        }

        return $availableAmount;
    }

    /**
     * Recalculates the shopping cart
     *
     * @param Cart|\XLite\Model\Cart $cart
     *
     * @return void
     * @throws \Exception
     */
    public function updateCart($cart)
    {
        $cart->updateOrder();

        Database::getRepo('XLite\Model\Cart')->update($cart, [], false);
        Database::getEM()->flush();
    }

    /**
     * Try to retrieve cart by token and store it in static parameter
     *
     * @param XCartContext $context
     *
     * @return \XLite\Model\Cart
     *
     * @throws ORMException
     * @throws \Exception
     */
    protected function retrieveCartForContext($context)
    {
        static $cart = null;

        if ($cart !== null) {
            return $cart;
        }

        /** @var Order|Cart $cart */
        if ($context->isAuthenticated() && $context->getLoggedProfile()) {
            $cart = Database::getRepo('XLite\Model\Cart')
                ->findOneBy([
                    'orig_profile' => $context->getLoggedProfile(),
                ]);
        }

        if (!$cart) {
            $cart = $this->createNewCart($context);
        } elseif (!$cart->getApiCartUniqueId()) {
            $cart->setApiCartUniqueId($cart->generateApiCartToken());
        }

        if (!$cart->getOrigProfile()) {
            /** @var Profile $profile */

            if ($context->isAuthenticated() && $context->getLoggedProfile()) {
                $profile = $context->getLoggedProfile();
            } else {
                $profile = new \XLite\Model\Profile();
                $profile->setAnonymous(true);
                $profile->setOrder($cart);
                Database::getEM()->persist($profile);
            }

            $cart->setProfile($profile);
            $cart->setOrigProfile($profile);
        }

        // TODO: currency set to null somewhere during PayPal checkout, find out why this bug happens
        if (!$cart->getCurrency()) {
            $cart->setCurrency(\XLite::getInstance()->getCurrency());
        }

        Database::getEM()->flush();

        if (
            $cart->getOrigProfile()
            && !$context->isAuthenticated()
            && $cart->getOrigProfile()->isPersistent()
        ) {
            $context->getAuthService()->loginProfile($cart->getOrigProfile());
        }

        return $cart;
    }

    /**
     * @param XCartContext $context
     *
     * @return Cart
     * @throws ORMException
     */
    protected function createNewCart($context)
    {
        /** @var Cart|Order $cart */
        $cart = new \XLite\Model\Cart();
        $cart->setApiCartUniqueId($cart->generateApiCartToken());

        Database::getEM()->persist($cart);

        return $cart;
    }
}
