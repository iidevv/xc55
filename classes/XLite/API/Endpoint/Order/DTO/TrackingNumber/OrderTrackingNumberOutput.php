<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\DTO\TrackingNumber;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class OrderTrackingNumberOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $date;

    /**
     * @var string
     */
    public string $value;
}
