<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params;

interface SetLineItemsInterface
{
    public const PARAM_LINE_ITEMS = "line_items";

    /**
     * @return void
     */
    public function setLineItems(): void;
}