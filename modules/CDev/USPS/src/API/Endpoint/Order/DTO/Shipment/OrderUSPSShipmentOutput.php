<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\API\Endpoint\Order\DTO\Shipment;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

class OrderUSPSShipmentOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank
     * @var string
     */
    public string $transaction_id;

    /**
     * @var array
     */
    public array $request_data = [];

    /**
     * @var array
     */
    public array $response_data = [];

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $print_date;

    /**
     * @Assert\PositiveOrZero()
     * @var float
     */
    public float $price;

    /**
     * @Assert\Length(max=32)
     * @var string
     */
    public string $shipment_id;

    /**
     * @Assert\Length(max=32)
     * @var string
     */
    public string $tracking_number;

    /**
     * @var array
     */
    public array $tracking_data = [];

    /**
     * @var string
     */
    public string $label_url;

    /**
     * @var array
     */
    public array $label_content = [];
}
