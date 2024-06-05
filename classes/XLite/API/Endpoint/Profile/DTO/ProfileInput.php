<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use XLite\Model\Profile;

class ProfileInput
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(min=5, max=128)
     * @var string
     */
    public string $login = '';

    /**
     * @Assert\Length(min=8, max=128)
     * @var string|null
     */
    public ?string $password = null;

    /**
     * @Assert\Choice({0,100})
     * @var int
     */
    public int $access_level = 0;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=1)
     * @Assert\Choice({"E","D"})
     * @ApiProperty(
     *      attributes={
     *          "openapi_context"={
     *              "type"="string",
     *              "enum"={"E", "D"},
     *              "example"="E",
     *          }
     *      }
     * )
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
