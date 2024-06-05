<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Params;

interface SetCustomPropertiesInterface
{
    public const PARAM_CUSTOM_PROPERTIES = "custom_properties";

    /**
     * @return void
     */
    public function setCustomProperties(): void;
}