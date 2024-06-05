<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\DTO;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

class OrderPaymentTransactionOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\Length(min=1, max=255)
     * @var string|null
     */
    public ?string $public_id;

    /**
     * @Assert\Length(min=1, max=16)
     * @var string|null
     */
    public ?string $public_txn_id;

    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $method_id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=16)
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
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=1)
     * @var string
     */
    public string $status;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=128)
     * @var string
     */
    public string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=255)
     * @var string
     */
    public string $local_name;

    /**
     * @var string
     */
    public string $currency;

    /**
     * @var float
     */
    public float $value;

    /**
     * @var string
     */
    public string $note;

    /**
     * @var Data\OrderPaymentTransactionDataOutput[]
     */
    public array $data;

    /**
     * @var BackendTransaction\OrderPaymentTransactionBackendTransactionOutput[]
     */
    public array $backend_transactions;
}
