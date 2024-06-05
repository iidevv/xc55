<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints;

interface ConstructorInterface
{
    /**
     * Collecting a constructed body
     *
     * @return void
     */
    public function build(): void;
}