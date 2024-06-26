<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Base;

/**
 * REST-based repository interface
 */
interface IREST
{
    /**
     * Get REST entity names
     *
     * @return array
     */
    public function getRESTNames();
}
