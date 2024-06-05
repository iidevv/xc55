<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\DTO;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use XLite\Model\Profile;

class ProfileOutput
{
    /**
     * @Assert\Positive()
     * @var int
     */
    public int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=128)
     * @var string
     */
    public string $login;

    /**
     * @Assert\PositiveOrZero
     * @var int
     */
    public int $access_level = 0;

    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ATOM})
     * @var DateTimeInterface
     */
    public DateTimeInterface $create_date;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ATOM})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $first_login_date;

    /**
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ATOM})
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $last_login_date;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=1)
     * @Assert\Choice({"E","D"})
     * @var string
     */
    public string $status = Profile::STATUS_ENABLED;

    /**
     * @var string
     */
    public string $status_comment = '';

    /**
     * @var string
     */
    public string $referer = '';

    /**
     * @Assert\Length(max=2)
     * @var string
     */
    public string $language = '';

    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $membership_id = null;

    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $pending_membership_id = null;

    /**
     * @var bool
     */
    public bool $force_change_password = false;

    /**
     * @var int[]
     */
    public array $role_ids = [];
}
