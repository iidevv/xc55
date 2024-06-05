<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Params;

interface SetBrandInterface
{
    public const PARAM_BRAND = "brand";

    /**
     * @return void
     */
    public function setBrand(): void;
}