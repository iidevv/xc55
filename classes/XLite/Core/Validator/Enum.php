<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Enumrable
 */
class Enum extends \XLite\Core\Validator\Enum\AEnum
{
    /**
     * Constructor
     *
     * @param array $list List of allowe values OPTIONAL
     *
     * @return void
     */
    public function __construct(array $list = [])
    {
        parent::__construct();

        $this->list = $list;
    }
}
