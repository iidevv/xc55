<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\DTO\OrderItem;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use XLite\API\Endpoint\Order\DTO\OrderItem\Surcharge\OrderItemSurchargeOutput as SurchargeOutput;

class OrderItemOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\Positive()
     * @var int
     */
    public int $product_id;

    /**
     * @var string
     */
    public string $sku;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $update_date;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var float
     */
    public float $price;

    /**
     * @var float
     */
    public float $item_net_price;

    /**
     * @var float
     */
    public float $discounted_subtotal;

    /**
     * @var int
     */
    public int $amount;

    /**
     * @var int
     */
    public int $backordered_amount;

    /**
     * @var SurchargeOutput[]
     */
    public array $surcharges;
}
