<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace\Normalizer;

class Simple extends \XLite\Core\Marketplace\Normalizer
{
    protected $filedName;

    /**
     * @param $filedName
     */
    public function __construct($filedName)
    {
        $this->filedName = $filedName;
    }

    public function normalize($response)
    {
        return $response[$this->filedName] ?? [];
    }
}
