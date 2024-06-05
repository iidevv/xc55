<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization;

interface SerializableEntity
{
    /**
     * Check if entity can be serialized
     *
     * @return boolean
     */
    public function isSerializable();
}
