<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\View\FormModel\Product;

use Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Database;
use XLite\Model\Product;
use Qualiteam\SkinActFullWidthImages\View\FormModel\Type\UploaderType;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    protected function defineFields()
    {
        $product = Database::getRepo(Product::class)->find($this->getDataObject()->default->identity);
        $full_width_images = [];

        if ($product) {
            $full_width_images = $product->getFullWidthImages();
        }

        $schema = parent::defineFields();

        $schema[self::SECTION_DEFAULT]['full_width_images'] = [
            'label'      => static::t('SkinActFullWidthImages full width images'),
            'type'       => UploaderType::class,
            'imageClass' => FullWidthImage::class,
            'files'      => $full_width_images,
            'multiple'   => true,
            'position'   => $schema[self::SECTION_DEFAULT]['images']['position'] + 10,
        ];

        return $schema;
    }
}