<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\View;


use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class Image extends \XLite\View\Image
{

    public function getProperties()
    {
        parent::getProperties();

        if ($this->getParam(self::PARAM_IMAGE)) {
            $image = $this->getParam(self::PARAM_IMAGE);
            if ($image instanceof \XLite\Model\Image\Product\Image) {
                $swatch = $image->getSwatch();
                if ($swatch) {
                    $this->properties['data-swatch-id'] = $image->getSwatch()->getId();
                } else {
                    $this->properties['data-swatch-id'] = 0;
                }

            }
        }

        return $this->properties;
    }
}