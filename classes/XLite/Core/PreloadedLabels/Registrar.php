<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\PreloadedLabels;

class Registrar extends \XLite\Base\Singleton
{
    protected $labels = [];

    public function register(array $data)
    {
        $this->labels = array_merge(
            $this->labels,
            $data
        );
    }

    public function getRegistered()
    {
        return $this->labels;
    }
}
