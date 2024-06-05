<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Helpers;

use Qualiteam\SkinActYotpoReviews\Helpers\Profile as ProfileHelper;
use Qualiteam\SkinActYotpoReviews\Validator\Helper\Profile as ProfileHelperValidator;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XLite\Model\Base\Surcharge;
use XLite\Model\Order as OrderModel;
use XLite\Model\OrderItem as OrderItemModel;

class Order
{
    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return string
     */
    public function getOrderNumber(?OrderModel $order): string
    {
        return $order ? $order->getOrderNumber() : '';
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return string
     */
    public function getOrderDate(?OrderModel $order): string
    {
        return $order ? date('Y-m-d', $order->getDate()) . 'T' . date('H:i:s', $order->getDate()) . 'Z' : '';
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return string
     */
    public function getOrderPaymentMethod(?OrderModel $order): string
    {
        return $order ? $order->getPaymentMethodName() : '';
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return float
     */
    public function getOrderTotalPrice(?OrderModel $order): float
    {
        return $order ? $order->getTotal() : 0;
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return float
     */
    public function getOrderSubtotalPrice(?OrderModel $order): float
    {
        return $order ? $order->getSubtotal() : 0;
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return string
     */
    public function getOrderPaymentStatus(?OrderModel $order): string
    {
        $status = '';

        if ($order) {
            $status = 'pending';

            if ($order->getTotal() === 0.00) {
                $status = 'paid';
            }
        }

        return $status;
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return array
     */
    public function getOrderCustomer(?OrderModel $order): array
    {
        $result   = [];
        $customer = $order?->getOrigProfile();

        if ($customer) {
            $helper = new ProfileHelper($customer);
            $validator = new ProfileHelperValidator($helper);

            $result = [
                'external_id'  => $helper->getCustomerExternalId(),
                'email'        => $helper->getCustomerEmail(),
                'first_name'   => $helper->getCustomerFirstName(),
                'last_name'    => $helper->getCustomerLastName(),
            ];

            if ($validator->isValidPhoneNumber()) {
                $result['phone_number'] = $helper->getCustomerPhoneNumber();
            }
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return array
     */
    public function getOrderBillingAddress(?OrderModel $order): array
    {
        $result   = [];
        $customer = $order?->getProfile();

        if ($customer
            && $customer->getBillingAddress()
        ) {
            $isShipping = $customer->getBillingAddress()->getIsShipping();

            if ($isShipping) {
                $result = $this->getOrderShippingAddress($order);
            } else {
                $helper = new BillingAddress($customer->getBillingAddress());

                $result = [
                    'address1'      => $helper->getBillingAddressStreet(),
                    'city'          => $helper->getBillingAddressCity(),
                    'state'         => $helper->getBillingAddressState(),
                    'zip'           => $helper->getBillingAddressZipcode(),
                    'province_code' => $helper->getBillingAddressProvinceCode(),
                    'country_code'  => $helper->getBillingAddressCountryCode(),
                    'phone_number'  => $helper->getBillingAddressPhone(),
                ];
            }
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return array
     */
    public function getOrderShippingAddress(?OrderModel $order): array
    {
        $result   = [];
        $customer = $order?->getProfile();

        if ($customer) {
            $helper = new ShippingAddress($customer->getShippingAddress());

            $result = [
                'address1'      => $helper->getShippingAddressStreet(),
                'city'          => $helper->getShippingAddressCity(),
                'state'         => $helper->getShippingAddressState(),
                'zip'           => $helper->getShippingAddressZipcode(),
                'province_code' => $helper->getShippingAddressProvinceCode(),
                'country_code'  => $helper->getShippingAddressCountryCode(),
                'phone_number'  => $helper->getShippingAddressPhone(),
            ];
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return array
     */
    public function getOrderLineItems(?OrderModel $order): array
    {
        $result = [];

        if ($order) {
            /** @var OrderItemModel $item */
            foreach ($order->getItems() as $item) {
                $result[] = [
                    'yotpo_product_id'    => $item->getProduct()?->getYotpoId(),
                    'external_product_id' => $item->getProduct()?->getSku(),
                    'quantity'            => $item->getAmount(),
                    'total_price'         => $item->getTotal(),
                    'subtotal_price'      => $this->getOrderSubtotal($item),
                ];
            }
        }

        return $result;
    }

    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return float
     */
    protected function getOrderSubtotal(OrderItemModel $item): float
    {
        return $item->getSurchargeTotalByType(Surcharge::TYPE_DISCOUNT);
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return array
     */
    public function getOrderCustomProperties(?OrderModel $order): array
    {
        $result = [];

        if ($order) {
            $result = [
                'coupon_used'        => $this->isOrderUsedCoupon($order),
                'coupon_code'        => $this->getOrderCouponCode($order),
                'used_reward_points' => $this->getOrderUsedRewardPoints($order),
                'tax_cost'           => $this->getOrderTax($order),
                'shipping_cost'      => $this->getOrderShippingCost($order),
                'discount_cost'      => $this->getOrderDiscount($order),
            ];
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return bool
     */
    protected function isOrderUsedCoupon(OrderModel $order): bool
    {
        $result              = false;
        $moduleManagerDomain = Container::getContainer()?->get(ModuleManagerDomain::class);

        if ($moduleManagerDomain?->isEnabled("CDev-Coupons")) {
            $result = count($order->getUsedCoupons()) > 0;
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return string
     */
    protected function getOrderCouponCode(OrderModel $order): string
    {
        $result              = '';
        $moduleManagerDomain = Container::getContainer()?->get(ModuleManagerDomain::class);

        if ($moduleManagerDomain?->isEnabled("CDev-Coupons")) {
            $coupons = [];
            $usedCoupons = $order->getUsedCoupons();

            if (count($usedCoupons) > 0) {

                /** @var \CDev\Coupons\Model\UsedCoupon $coupon */
                foreach ($usedCoupons as $coupon) {
                    $coupons[] = $coupon->getActualCode();
                }
            }

            $result = count($coupons) > 0
                ? implode(', ', $coupons)
                : '';
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return int
     */
    protected function getOrderUsedRewardPoints(OrderModel $order): int
    {
        $result              = 0;
        $moduleManagerDomain = Container::getContainer()?->get(ModuleManagerDomain::class);

        if ($moduleManagerDomain?->isEnabled("QSL-LoyaltyProgram")) {
            $result = $order->getRewardPoints() && $order->isUserInLoyaltyProgram()
                ? $order->getRedeemedRewardPoints()
                : 0;
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected function getOrderTax(OrderModel $order): float
    {
        return $order->getSurchargeSumByType(Surcharge::TYPE_TAX);
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected function getOrderShippingCost(OrderModel $order): float
    {
        return $order->getSurchargeSumByType(Surcharge::TYPE_SHIPPING);
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected function getOrderDiscount(OrderModel $order): float
    {
        return $order->getSurchargeSumByType(Surcharge::TYPE_DISCOUNT);
    }
}