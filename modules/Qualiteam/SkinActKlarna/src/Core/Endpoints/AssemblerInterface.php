<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints;

interface AssemblerInterface
{
    /**
     * Assembling a data to call endpoint
     */
    public function assemble(): void;
}