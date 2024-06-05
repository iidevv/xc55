<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization\Deserializer;

class SchemaArrayObject extends SchemaGroup
{
    /**
     * SchemaArrayObject constructor.
     *
     * @param string      $buffer
     * @param SchemaGroup $parent
     */
    public function __construct(string $buffer, SchemaGroup $parent = null)
    {
        $count = (int) substr($buffer, 2, -1);

        parent::__construct($count, $parent);
    }
}
