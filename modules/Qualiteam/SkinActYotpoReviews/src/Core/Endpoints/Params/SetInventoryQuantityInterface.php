<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params;

interface SetInventoryQuantityInterface
{
    public const PARAM_INVENTORY_QUANTITY = "inventory_quantity";

    /**
     * @return void
     */
    public function setInventoryQuantity(): void;
}