<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer;

use XLite\API\Endpoint\Order\DTO\BaseOutput;
use XLite\API\Endpoint\Order\Transformer\OrderItem\OutputTransformerInterface as OrderItemTransformerInterface;
use XLite\API\Endpoint\Order\Transformer\PaymentStatus\OutputTransformerInterface as PaymentStatusTransformerInterface;
use XLite\API\Endpoint\Order\Transformer\ShippingStatus\OutputTransformerInterface as ShippingStatusTransformerInterface;
use XLite\API\Endpoint\Order\Transformer\Surcharge\OutputTransformerInterface as SurchargeTransformerInterface;
use XLite\API\Endpoint\Order\Transformer\TrackingNumber\OutputTransformerInterface as TrackingNumberTransformerInterface;
use XLite\API\Endpoint\ProfileAddress\Transformer\OutputTransformerInterface as OrderAddressInterface;
use DateTimeImmutable;

abstract class OutputTransformerAbstract
{
    protected PaymentStatusTransformerInterface $paymentStatusTransformer;

    protected ShippingStatusTransformerInterface $shippingStatusTransformer;

    protected SurchargeTransformerInterface $surchargeStatusTransformer;

    protected OrderItemTransformerInterface $orderItemTransformer;

    protected OrderAddressInterface $orderAddressTransformer;

    protected TrackingNumberTransformerInterface $trackingNumberTransformer;

    public function __construct(
        PaymentStatusTransformerInterface $paymentStatusTransformer,
        ShippingStatusTransformerInterface $shippingStatusTransformer,
        SurchargeTransformerInterface $surchargeStatusTransformer,
        OrderItemTransformerInterface $orderItemTransformer,
        OrderAddressInterface $orderAddressTransformer,
        TrackingNumberTransformerInterface $trackingNumberTransformer
    ) {
        $this->paymentStatusTransformer = $paymentStatusTransformer;
        $this->shippingStatusTransformer = $shippingStatusTransformer;
        $this->surchargeStatusTransformer = $surchargeStatusTransformer;
        $this->orderItemTransformer = $orderItemTransformer;
        $this->orderAddressTransformer = $orderAddressTransformer;
        $this->trackingNumberTransformer = $trackingNumberTransformer;
    }

    protected function basicTransform(BaseOutput $dto, $object, string $to, array $context = []): BaseOutput
    {
        $origProfile = $object->getOrigProfile();
        $profile = $object->getProfile();
        $paymentStatus = $object->getPaymentStatus();
        $shippingStatus = $object->getShippingStatus();
        $dto->id = $object->getOrderId();
        $dto->currency = $object->getCurrency()->getCode();
        $dto->create_date = new DateTimeImmutable('@' . $object->getDate());
        $dto->update_date = new DateTimeImmutable('@' . $object->getLastRenewDate());
        $dto->customer_id = $origProfile ? $origProfile->getProfileId() : null;
        $dto->admin_notes = $object->getAdminNotes();
        $dto->notes = $object->getNotes();
        $dto->order_profile_id = $profile ? $profile->getProfileId() : null;
        $dto->payment_status = $paymentStatus ? $this->paymentStatusTransformer->transform($paymentStatus, $to, $context) : null;
        $dto->shipping_id = $object->getShippingId();
        $dto->shipping_method_name = $object->getShippingMethodName();
        $dto->shipping_status = $shippingStatus ? $this->shippingStatusTransformer->transform($shippingStatus, $to, $context) : null;
        $dto->stock_status = $object->getStockStatus();
        $dto->sub_total = $object->getSubtotal();
        $dto->total = $object->getTotal();
        $dto->email = $profile ? $profile->getEmail() : '';
        $dto->billing_address = $profile && $profile->getBillingAddress()
            ? $this->orderAddressTransformer->transform($profile->getBillingAddress(), $to, $context)
            : null;
        $dto->shipping_address = $profile && $profile->getShippingAddress()
            ? $this->orderAddressTransformer->transform($profile->getShippingAddress(), $to, $context)
            : null;

        $dto->surcharges = [];
        foreach ($object->getSurcharges() as $surcharge) {
            $dto->surcharges[] = $this->surchargeStatusTransformer->transform($surcharge, $to, $context);
        }

        $dto->items = [];
        foreach ($object->getItems() as $item) {
            $dto->items[] = $this->orderItemTransformer->transform($item, $to, $context);
        }

        $dto->tracking_numbers = [];
        foreach ($object->getTrackingNumbers() as $trackingNumber) {
            $dto->tracking_numbers[] = $this->trackingNumberTransformer->transform($trackingNumber, $to, $context);
        }

        return $dto;
    }
}
