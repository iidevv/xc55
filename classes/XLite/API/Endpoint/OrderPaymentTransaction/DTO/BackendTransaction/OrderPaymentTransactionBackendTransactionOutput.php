<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\DTO\BackendTransaction;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

class OrderPaymentTransactionBackendTransactionOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\Length(min=1, max=255)
     * @var string
     */
    public string $type;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $date;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=1)
     * @var string
     */
    public string $status;

    /**
     * @var float
     */
    public float $value;

    /**
     * @var Data\OrderPaymentTransactionBackendTransactionDataOutput[]
     */
    public array $data;
}
