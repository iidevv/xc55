<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

/**
 * Image
 */
class CommonImage extends \XLite\View\Image
{
    /**
     * @inheritdoc
     */
    public function getProperties()
    {
        $props = parent::getProperties();

        foreach (['width', 'height'] as $key) {
            if (isset($props[$key])) {
                unset($props[$key]);
            }
        }

        return $props;
    }
}
