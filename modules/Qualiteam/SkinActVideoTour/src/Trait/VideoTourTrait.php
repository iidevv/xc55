<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Trait;

/**
 * Trait video tour
 */
trait VideoTourTrait
{
    /**
     * Get module path
     *
     * @return string
     */
    public function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActVideoTour';
    }

    /**
     * Get video tours label
     *
     * @return string
     */
    public function getVideoToursLabel(): string
    {
        return 'Video tours';
    }
}