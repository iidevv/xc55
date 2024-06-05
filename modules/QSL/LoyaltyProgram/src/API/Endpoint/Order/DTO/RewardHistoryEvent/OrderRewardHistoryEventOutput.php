<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\API\Endpoint\Order\DTO\RewardHistoryEvent;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class OrderRewardHistoryEventOutput
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
     * @var int
     */
    public int $points;

    /**
     * @var string
     */
    public string $reason;

    /**
     * @var string
     */
    public string $comment;

    /**
     * @Assert\Positive
     * @var int|null
     */
    public ?int $user_id;

    /**
     * @Assert\Positive
     * @var int|null
     */
    public ?int $initiator_id;
}
