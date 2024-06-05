<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\API\Endpoint\Order\DTO;

use DateTimeInterface;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput as ExtendedOutput;
use QSL\AbandonedCartReminder\API\Endpoint\Order\DTO\Email\OrderAbandonedCartReminderEmailOutput as EmailOutput;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * @Extender\Mixin
 */
class Output extends ExtendedOutput
{
    /**
     * @Assert\PositiveOrZero
     * @var int
     */
    public int $recovered;

    /**
     * @Assert\PositiveOrZero
     * @var int
     */
    public int $cart_reminders_sent;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $cart_reminder_date;

    /**
     * @var EmailOutput[]
     */
    public array $cart_reminder_emails = [];

    /**
     * @var EmailOutput|null
     */
    public ?EmailOutput $cart_recovery_email;

    /**
     * @Assert\PositiveOrZero
     * @var int
     */
    public int $lost;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $last_visit_date;
}
