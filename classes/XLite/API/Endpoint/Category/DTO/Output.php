<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\DTO;

use XLite\API\Endpoint\CategoryBanner\DTO\BannerOutput;
use XLite\API\Endpoint\CategoryIcon\DTO\IconOutput;
use XLite\API\Endpoint\Membership\DTO\MembershipOutput;

class Output
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var bool
     */
    public bool $enabled;

    /**
     * @var bool
     */
    public bool $show_title;

    /**
     * @var int
     */
    public int $position;

    /**
     * @var MembershipOutput[]
     */
    public array $memberships;

    /**
     * @var int|null
     */
    public ?int $parent;

    /**
     * @var string
     */
    public string $clean_url;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var string
     */
    public string $meta_tags;

    /**
     * @var string
     */
    public string $meta_description;

    /**
     * @var string
     */
    public string $meta_title;

    /**
     * @var IconOutput|null
     */
    public ?IconOutput $icon;

    /**
     * @var BannerOutput|null
     */
    public ?BannerOutput $banner;
}
