<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Core\Endpoints\Trackings;

use Qualiteam\SkinActAftership\Core\Endpoints\Params\GetSlugInterface;
use Qualiteam\SkinActAftership\Core\Endpoints\Params\GetTrackingNumberInterface;
use Qualiteam\SkinActAftership\Core\Endpoints\Params\SetSlugInterface;
use Qualiteam\SkinActAftership\Core\Endpoints\Params\SetTrackingNumberInterface;

/**
 * Class get tracking
 */
class GetTrackings extends \Qualiteam\SkinActAftership\Core\Endpoints\GetEndpoint implements SetSlugInterface, SetTrackingNumberInterface, GetSlugInterface, GetTrackingNumberInterface
{
    /**
     * @var string
     */
    protected string $slug;

    /**
     * @var string
     */
    protected string $trackingNumber;

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return void
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Get tracking number
     *
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * Set tracking number
     *
     * @param string|int $trackingNumber
     */
    public function setTrackingNumber(string|int $trackingNumber): void
    {
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * Collect path elements
     *
     * @return array
     */
    protected function preparePath(): array
    {
        return [
            $this->getSlug(),
            $this->getTrackingNumber(),
        ];
    }
}