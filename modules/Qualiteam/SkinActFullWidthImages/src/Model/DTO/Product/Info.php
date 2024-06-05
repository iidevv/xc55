<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\Model\DTO\Product;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $objectFullWidthImages = $object->getFullWidthImages();
        $full_width_images       = [0 => [
            'delete'   => false,
            'position' => '',
            'alt'      => '',
            'temp_id'  => '',
        ]];
        foreach ($objectFullWidthImages as $image) {
            $full_width_images[$image->getId()] = [
                'delete'   => false,
                'position' => '',
                'alt'      => '',
                'temp_id'  => '',
            ];
        }

        $this->default->full_width_images = $full_width_images;
    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->processFiles('full_width_images', $this->default->full_width_images);
    }
}