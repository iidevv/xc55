<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\DTO;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use XLite\API\Endpoint\ProfileAddress\DTO\ProfileAddressOutput;
use XLite\API\Endpoint\Order\DTO\OrderItem\OrderItemOutput;
use XLite\API\Endpoint\Order\DTO\PaymentStatus\OrderPaymentStatusOutput;
use XLite\API\Endpoint\Order\DTO\ShippingStatus\OrderShippingStatusOutput;
use XLite\API\Endpoint\Order\DTO\Surcharge\OrderSurchargeOutput;
use XLite\API\Endpoint\Order\DTO\TrackingNumber\OrderTrackingNumberOutput;

abstract class BaseOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\PositiveOrZero
     * @var float
     */
    public float $total;

    /**
     * @Assert\PositiveOrZero
     * @var float
     */
    public float $sub_total;

    /**
     * @var string
     */
    public string $email;

    /**
     * @var OrderItemOutput[]
     */
    public array $items;

    /**
     * @var ProfileAddressOutput|null
     */
    public ?ProfileAddressOutput $billing_address;

    /**
     * @var ProfileAddressOutput|null
     */
    public ?ProfileAddressOutput $shipping_address;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $create_date;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $update_date;

    /**
     * @var OrderPaymentStatusOutput|null
     */
    public ?OrderPaymentStatusOutput $payment_status;

    /**
     * @var OrderShippingStatusOutput|null
     */
    public ?OrderShippingStatusOutput $shipping_status;

    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $customer_id;

    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $order_profile_id;

    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $shipping_id;

    public ?string $shipping_method_name;

    /**
     * @var string
     */
    public string $notes;

    /**
     * @var string
     */
    public string $admin_notes;

    /**
     * @var string
     */
    public string $currency;

    /**
     * @var string
     */
    public string $stock_status;

    /**
     * @var OrderSurchargeOutput[]
     */
    public array $surcharges;

    /**
     * @var OrderTrackingNumberOutput[]
     */
    public array $tracking_numbers;
}
