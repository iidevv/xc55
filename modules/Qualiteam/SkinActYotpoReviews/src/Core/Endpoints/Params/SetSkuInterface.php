<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params;

interface SetSkuInterface
{
    public const PARAM_SKU = "sku";

    /**
     * @return void
     */
    public function setSku(): void;
}